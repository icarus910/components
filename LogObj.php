<?php
/**
 * Created by PhpStorm.
 * User: ZiiiQ
 * Date: 2019/01/25
 * Time: 15:43
 */

namespace common\components;

/**
 * 日志行为
 * @property string $_subject 日志名
 * @property array $_logArray  日志主体
 */
class LogObj
{
    private $_subject;
    private $_logArray = [];

    /**
     * LogObj constructor.
     * @param string $subject 主题名
     */
    function __construct($subject)
    {
        $this->_subject = $subject;
    }

    /**
     * 组装日志
     * @param $log
     */
    public function set($log)
    {
        array_push($this->_logArray, $log);
    }

    /**
     * 请求结束销毁变量记录日志
     */
    function __destruct()
    {
        //saveLog();
    }
}