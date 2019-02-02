<?php
/**
 * Created by PhpStorm.
 * User: ZiiiQ
 * Date: 2019/02/02
 * Time: 17:22
 */
namespace common\components;

class MemCache
{

    const MEMCACHE_SECOND = 1;//缓存时间——秒

    const MEMCACHE_MINUTE = 60;//分

    const MEMCACHE_HALF_HOUR = 1800;//半小时

    const MEMCACHE_HOUR = 3600;//一小时

    const MEMCACHE_DAY = 86400;//一天

    const MEMCACHE_WEEK = 604800; //一周

    const MEMCACHE_YEAR = 31536000;

    public $useMemcached = true;//覆盖该参数


    protected static $cache = null;

    /**
     * 获取单例CACHE对象
     *
     */
    protected static function getCacheObj()
    {
        if (self::$cache === null) {
            self::$cache = Yii::$app->cache;
        }

        return self::$cache;
    }

    /**
     * 获取缓存并主动设置缓存-推荐使用
     * eg: MemCache::getInstance()->getForCache('memcacheKey',array($this, 'your class's function'),[function's params]);
     * @param $key string 键
     * @param $func callable 回调方法
     * @param $params [] 回调方法需要的参数
     * @param int $expire 失效时间，秒
     * @return bool|mixed|string
     */
    public static function getForCache($key, $func = [], $params = [], $expire = self::MEMCACHE_MINUTE)
    {
        $value = false;
        if (YII_ENV_PROD) {
            $value = self::getCache($key);
        }

        if ($value === false && !empty($func)) {
            $value = call_user_func_array($func, $params);
            if ($value) {
                YII_ENV_PROD && self::setCache($key, $value, $expire);
            }
        }

        return $value;
    }

    /**
     * 设置缓存
     * @param $key string 键
     * @param $value mixed 值
     * @param int $expire int 失效时间，秒
     * @return bool
     */
    public static function setCache($key, $value, $expire = self::MEMCACHE_MINUTE)
    {
        return self::getCacheObj()->set($key, $value, $expire);
    }

    /**
     * 获取缓存
     * @param $key
     * @return mixed
     */
    public static function getCache($key)
    {
        return self::getCacheObj()->get($key);
    }

    /**
     * 删除缓存
     * @param $key string 键
     * @return bool
     */
    public static function delCache($key)
    {
        return self::getCacheObj()->delete($key);
    }

    /**
     * 计数器
     * 腾讯云缓存未知问题，请勿使用...可以使用redis
     * @param $key
     * @param int $expire 时间范围
     * @return bool|int|mixed 如果返回int，则成功  返回false，则计数器不成功
     */
    public static function incr($key, $expire = self::MEMCACHE_DAY)
    {
        if (self::getCacheObj()->add($key, 1, $expire)) {
            $count = 0;
        } else {
            $count = self::getCache($key);
        }
        $result = self::setCache($key, ++$count, $expire);
        if ($result) {
            return $count;
        }

        return false;
    }

    /**
     * 复写setValue方法，因为腾讯云memcache服务器只支持一种
     * @param string $key
     * @param string $value
     * @param int $expire
     * @return bool
     */
    protected function setValue($key, $value, $expire)
    {
        return $this->useMemcached ? $this->getMemcache()->set($key, $value, $expire) : $this->getMemcache()->set(
            $key,
            $value,
            0,
            $expire
        );
    }
}
