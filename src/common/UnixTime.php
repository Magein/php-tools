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
     * 转化为时间戳，
     * @param $date
     * @return bool|false|int
     */
    public function dateEnd($date)
    {
        $datetime = $date . ' 23:59:59';

        return $this->unix($datetime);
    }
}