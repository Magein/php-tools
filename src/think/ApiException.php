<?php

namespace magein\php_tools\think;

use think\Config;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\PDOException;
use think\exception\ThrowableError;

class ApiException extends Handle
{
    public function render(\Exception $e)
    {
        $code = 0;

        if ($e instanceof HttpException) {
            $message = $e->getMessage();
            $data = $e->getHeaders();
        } else {

            /**
             * 这里的错误信息，目前之分为了两种
             *
             * 1. 数据库错误
             *
             * 2. php执行错误（参考think下的错误类）
             *
             *
             */

            $message = 'error ：';
            $data = $e->getMessage();
            if ($e instanceof PDOException) {
                $message .= 'db';
                $data = $e->getMessage();
            } else {
                $message .= 'system';
                $data = [
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ];
            }
        }

        if (Config::get('app_debug') === false) {
            $data = [];
        }

        return ApiReturn::instance()->create($code, $message, $data);
    }
}