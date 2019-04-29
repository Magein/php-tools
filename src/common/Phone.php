<?php

namespace magein\php_tools\common;

use magein\php_tools\traits\Instance;

class Phone
{
    use Instance;

    /**
     * 手机号码为空
     */
    const PHONE_NUMBER_NULL = 10010;

    /**
     * 手机号码不正确
     */
    const PHONE_NUMBER_NOT_RIGHT = 10011;

    /**
     * @var null|static 实例对象
     */
    protected static $instance = null;

    /**
     * 获取示例
     * @param array $options 实例配置
     * @return static
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) self::$instance = new self($options);

        return self::$instance;
    }

    /**
     * @var int
     */
    private $code;

    /**
     * @param $code
     * @return mixed
     */
    private function setCode($code)
    {
        return $this->code = $code;
    }

    /**
     * @return int
     */
    public function getLastCode()
    {
        return $this->code;
    }

    /**
     * @param null|int $code
     * @return mixed|string
     */
    public function getError($code = null)
    {
        $code = $code ? $code : $this->code;
        $message = [
            self::PHONE_NUMBER_NULL => '手机号码不能为空',
            self::PHONE_NUMBER_NOT_RIGHT => '手机号码不正确'
        ];
        return isset($message[$code]) ? $message[$code] : '';
    }

    /**
     * @param string $phone
     * @return bool
     */
    public function checkPhone($phone)
    {
        if (empty($phone)) {
            $this->setCode(self::PHONE_NUMBER_NULL);
            return false;
        }
        if (!preg_match('/^[0-9]{11}$/', $phone)) {
            $this->setCode(self::PHONE_NUMBER_NOT_RIGHT);
            return false;
        }
        if (!preg_match("/^13[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|16[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|19[0-9]{1}[0-9]{8}$/", $phone)) {
            $this->setCode(self::PHONE_NUMBER_NOT_RIGHT);
            return false;
        }
        return true;
    }

    /**
     * @param string $phone
     * @return string
     */
    public function hideMiddleFourNumber($phone)
    {
        if ($this->checkPhone($phone)) {
            return preg_replace('/([0-9]{3})[0-9]{4}([0-9]{3})/', '$1****$2', $phone);
        }
        return $phone;
    }
}