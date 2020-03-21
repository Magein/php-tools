<?php

namespace magein\php_tools\common;


use magein\php_tools\traits\Error;
use magein\php_tools\traits\Instance;

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
     * 当获取周、月的时间段的时候，是否包含今天
     * @var bool
     */
    private $contain_today = true;

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
        } else {
            if (strlen($date) > 10) {
                $date = substr($date, 0, 10);
            }
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
        } else {
            if (strlen($date) > 10) {
                $date = substr($date, 0, 10);
            }
        }

        $datetime = $date . ' 00:00:00';

        return $this->unix($datetime);
    }

    /**
     * @param bool $bool
     * @return $this
     */
    public function setContainToday($bool = true)
    {
        $this->contain_today = $bool === true ? true : false;

        return $this;
    }

    /**
     * 每个月的开始时间是固定的 xx月-01开始
     * @param null $month
     * @return false|string
     */
    public function monthStart($month = null)
    {
        if (empty($month)) {
            $month = date('m');
        }

        if (strlen($month) > 2) {
            $month = date('m', $this->unix($month));
        } else {
            $month = intval($month);
        }

        if ($month < 1 || $month > 12) {
            $month = 1;
        }

        return date('Y-' . $month . '-01');
    }

    /**
     * 获取每个月的结束时间
     * @param null $month
     * @return false|string
     */
    public function monthEnd($month = null)
    {
        $timestamp = ($this->monthStartUnix($month));

        $timestamp = strtotime('+1 month', $timestamp) - 1;

        return $this->date($timestamp);
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
     * @return bool|false|int
     */
    public function monthEndUnix($month = null)
    {
        return $this->unix($this->monthEnd($month));
    }

    /**
     * 获取自然月的范围
     * @param string $date
     * @param string $format
     * @return array|string
     */
    public function monthRange($date = '', $format = 'unix')
    {
        if ($format === 'unix') {
            $start = $this->monthStartUnix($date);
            $end = $this->monthEndUnix($date);
        } else {
            $start = $this->monthStart($date);
            $end = $this->monthEnd($date);

            var_dump($start);
            var_dump($end);

            if ($format === 'datetime') {
                $start .= ' 00:00:00';
                $end .= ' 23:59:59';
            }
        }

        return [
            $start,
            $end
        ];
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

    /**
     * 获取日期所在的自然周范围
     * @param string $date
     * @param bool $to_unix
     * @return array
     */
    public function weekRange($date = '', $to_unix = true)
    {
        if (empty($date)) {
            $date = $this->date();
        }

        $date = $this->unix(substr($date, 0, 10));

        if (false === $date) {
            return [];
        }

        $week_start_day = strtotime('this week', $date);
        $week_end_day = $week_start_day + 86400 * 7 - 1;

        if ($to_unix) {
            $result = [
                $week_start_day,
                $week_end_day
            ];
        } else {
            $result = [
                $this->dateTime($week_start_day),
                $this->dateTime($week_end_day),
            ];
        }

        return $result;
    }

    /**
     * 传递的时间往前推一周，即七天的数据
     * @param string $date
     * @param bool $to_unix
     * @return array
     */
    public function weekEnd($date = '', $to_unix = true)
    {
        if (empty($date)) {
            $date = $this->date();
        }

        if (false === $date) {
            return [];
        }

        $week_end_day = $this->unix(substr($date, 0, 10));

        if ($this->contain_today) {
            $week_end_day = $week_end_day + 86400 - 1;
        }

        $week_start_day = $week_end_day - 86400 * 7;

        if ($to_unix) {
            $result = [
                $week_start_day,
                $week_end_day
            ];
        } else {
            $result = [
                $this->dateTime($week_start_day),
                $this->dateTime($week_end_day),
            ];
        }

        return $result;
    }
}