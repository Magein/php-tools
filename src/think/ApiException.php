<?php

namespace magein\php_tools\think;

use think\exception\Handle;
use think\exception\HttpException;

class ApiException extends Handle
{
    public function render(\Exception $e)
    {
        // http请求错误
        if ($e instanceof HttpException) {
            return ApiReturn::instance()->create($e->getStatusCode(), $e->getMessage(), $e->getHeaders());
        }
    }
}