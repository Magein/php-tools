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
     * 每个月开始的时间
     * @param null $month
     * @return false|string
     */
    public function monthStart($month = null)
    {
        if (empty($month)) {
            $month = date('m');
        }

        $month = intval($month);

        if ($month < 1 || $month > 12) {
            $month = 1;
        }

        return date('Y-' . $month . '-01');
    }

    /**
     * 每个月开始的unix时间
     * @param null $month
     * @return bool|false|int
     */
    public function monthStartUnix($month = null)
    {
        return $this->unix($this->monthStart($month));
    }

    /**
     * @param null $month
     * @return false|string
     */
    public function monthEnd($month = null)
    {
        $timestamp = ($this->monthStartUnix($month)) - 1;

        $timestamp = strtotime(date('Y-m-d', $timestamp));

        $timestamp = strtotime('+1 month -1 day', $timestamp);

        return $this->date($timestamp);
    }

    /**
     * @param null $month
     * @return bool|false|int
     */
    public function monthEndUnix($month = null)
    {
        return $this->unix($this->monthEnd($month));
    }

    /**
     * @param bool $unix
     * @param bool $to_array
     * @return array|string
     */
    public function monthRange($unix = true, $to_array = true)
    {
        if ($unix) {
            $start = $this->monthStartUnix();
            $end = $this->monthEndUnix();
        } else {
            $start = $this->monthStart();
            $end = $this->monthEnd();
        }

        if ($to_array) {
            return [
                $start,
                $end
            ];
        }
        return $start . ' ~ ' . $end;
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