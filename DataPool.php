<?php
/**
 * Created by PhpStorm.
 * User: ZiiiQ
 * Date: 2019/01/25
 * Time: 15:33
 */

namespace common\components;

/**
 * 数据池
 * @property array $_dataArray  数据主体
 */
class DataPool
{
    private static $dataArray = [];

    /**
     * 数据池
     * @param callable | array $func
     * @param array $params
     * @return mixed
     */
    public static function get($func = [], $params = [])
    {
        $key = json_encode(array_merge($func, $params));
        if (isset(self::$dataArray[$key])) {
            return self::$dataArray[$key];
        }
        $data = call_user_func_array($func, $params);
        return self::$dataArray[$key] = $data;
    }

    /**
     * 清除数据
     * @param callable | array $func
     * @param array $params
     */
    public static function clear($func = [], $params = [])
    {
        $key = json_encode(array_merge($func, $params));
        if (isset(self::$dataArray[$key])) {
            unset(self::$dataArray[$key]);
        }
    }
}