<?php
/**
 * Created by PhpStorm.
 * User: ZiiiQ
 * Date: 2019/01/25
 * Time: 15:17
 */

namespace common\components;

/**
 * 自动加锁执行
 */
class Synchronized
{
    /**
     * 自动加锁执行
     * @param $lock string 锁key
     * @param callable|array $func
     * @param array $params
     * @param int $waitTime 等待时间，微秒，默认相当于是100毫秒
     * @param int $maxExecuteTime 最大执行时间 秒
     * @return mixed
     */
    public static function lockAndTry($lock, $func = [], $params = [], $waitTime = 100000, $maxExecuteTime = 120)
    {

        for ($i = 0; $i < 10; $i++) {
            if (Redis::setnx($lock, 1, $maxExecuteTime)) {
                try {
                    return call_user_func_array($func, $params);
                } catch (Exception $e) {
                    throw $e;
                } finally {
                    Redis::delKey($lock);
                }
            }
            //等待时间随着循环次数增加
            $realWaitTime = $waitTime * $i ? $waitTime * $i : $waitTime;
            //等待，微秒
            usleep($realWaitTime);
        }
        //异常处理
    }
}