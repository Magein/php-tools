<?php

namespace magein\php_tools\common;


use magein\php_tools\traits\Error;
use magein\php_tools\traits\Instance;
use magein\render\admin\RenderForm;

/**
 * 处理时间
 * Class UnixTime
 * @package magein\php_tools\common
 */
class UnixTime
{
    use Instance;

    use Error;

    /**
     * 转化为时间戳
     * @param $datetime
     * @return bool|false|int
     */
    public function unix($datetime)
    {
        if (empty($datetime)) {
            $this->setError('请输入时间');
            return false;
        }

        if (preg_match('/^1[0-9]{9}/', $datetime)) {
            return $datetime;
        }

        $result = strtotime($datetime);

        if (false === $result) {
            $this->setError('时间格式错误');
            return false;
        }

        if ($result < 0) {
            $this->setError('时间格式错误');
            return false;
        }

        return $result;
    }

    /**
     * 一天结束时间点
     * @param string $date
     * @return bool|false|int
     */
    public function endDay($date = '')
    {
        if (empty($date)) {
            $date = date('Y-m-d', time());
        }

        $datetime = $date . ' 23:59:59';

        return $this->unix($datetime);
    }

    /**
     * 一天的开始时间点
     * @param string $date
     * @return bool|false|int
     */
    public function startDay($date = '')
    {
        if (empty($date)) {
            $date = date('Y-m-d', time());
        }

        $datetime = $date . ' 00:00:00';

        return $this->unix($datetime);
    }

    /**
     * 今天的时间时间戳
     * @return array
     */
    public function today()
    {
        return [$this->startDay(), $this->endDay()];
    }

    /**
     *
     * @param string $unix_time
     * @param string $format
     * @return false|string
     */
    public function dateTime($unix_time = '', $format = 'Y-m-d H:i:s')
    {

        if (empty($unix_time)) {
            $unix_time = time();
        }

        return date($format, $unix_time);
    }

    /**
     * @param string $unix_time
     * @return false|string
     */
    public function date($unix_time = '')
    {
        return $this->dateTime($unix_time, 'Y-m-d');
    }

    /**
     * @param string $unix_time
     * @return false|string
     */
    public function time($unix_time = '')
    {
        return $this->dateTime($unix_time, 'H:i:s');
    }
}