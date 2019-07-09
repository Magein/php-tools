<?php

namespace magein\php_tools\think;

use think\Request;

/**
 * 接口行为验证
 * Class ApiBehavior
 * @package app\common
 */
class ApiSession
{
    /**
     * @param $param
     * @return string
     */
    public static function id($id = '')
    {
        if (empty($id)) {
            $id = Request::instance()->header('X-Request-Session-Ticket');
        }

        return $id;
    }
}