<?php

namespace magein\php_tools\think;

use magein\php_tools\traits\Error;

class ApiService
{
    use Error;

    /**
     * @var null
     */
    protected static $instance = [];

    /**
     * 在同一个生命周期内的类实力唯一
     * @return static
     */
    public static function instance()
    {
        if (!isset(self::$instance[static::class])) {
            self::$instance[static::class] = new static();
        }
        return self::$instance[static::class];
    }

    /**
     * @param null $result
     * @return \think\response\Json
     */
    public function response($result = null)
    {
        return ApiReturn::instance()->create($this->getCode(), $this->getError(), $result);
    }

    /**
     * @param $message
     * @param int $code
     * @param null $result
     * @return \think\response\Json
     */
    public function success($result = null, $code = 1, $message = '')
    {
        return ApiReturn::instance()->create($code, $message, $result);
    }

    /**
     * @param $message
     * @param int $code
     * @param null $result
     * @return \think\response\Json
     */
    public function error($message, $code = 0, $result = null)
    {
        return ApiReturn::instance()->create($code, $message, $result);
    }
}