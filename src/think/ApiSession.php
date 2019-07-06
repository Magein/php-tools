<?php

namespace magein\php_tools\think;

use think\Exception;
use think\exception\HttpException;
use think\Request;
use think\Session;

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
    public static function init($param)
    {
        $config = config('session');

        if (is_string($param)) {
            $config['id'] = $param;
        } else {
            $config = array_merge($config, $param);
        }

        $module = Request::instance()->module() ?: 'think';

        if (!isset($config['prefix']) || empty($config['prefix']) || $config['prefix'] == 'think') {
            $config['prefix'] = $module;
        }

        try {
            Session::init($config);
        } catch (Exception $exception) {
            throw new HttpException(1000, '服务器内部出错误');
        }

        return $config['id'];
    }
}