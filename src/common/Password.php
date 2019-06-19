<?php

namespace magein\php_tools\common;


use magein\php_tools\traits\Instance;

class Password
{
    use Instance;

    /**
     * 加密的秘钥
     * @var string
     */
    private $secret_key = 'secret_key';

    /**
     * 检测密码合法性
     * @var bool
     */
    private $legal_verify = true;

    /**
     * 验证密码合法性的正则表达式
     * @var string
     */
    private $legal_regular = '^[\w]{6,18}$';

    /**
     * Password constructor.
     * @param null $secret_key
     * @param null $regular
     */
    public function __construct($secret_key = null, $regular = null)
    {
        $this->setSecretKey($secret_key);
        $this->setLegalRegular($regular);
    }

    /**
     * 关闭密码的合法性验证
     */
    public function closeLegalVerify()
    {
        $this->legal_verify = false;
    }

    /**
     * @param $key
     * @return mixed|string
     */
    private function getConfig($key)
    {
        $value = '';

        if (class_exists('think\Config')) {

            $key = 'password.' . $key;

            $value = call_user_func_array('think\Config::get', [$key]);
        };

        return $value;
    }

    /**
     * @param $secret_key
     * @return string
     */
    private function setSecretKey($secret_key)
    {
        if (empty($secret_key)) {

            $secret_key = $this->getConfig('secret_key');

            $this->secret_key = $secret_key ?: $this->secret_key;
        }

        return $this->secret_key;
    }

    /**
     * @param $regular
     * @return string
     */
    public function setLegalRegular($regular)
    {
        if (empty($regular)) {

            $regular = $this->getConfig('legal_regular');

            $this->legal_regular = $regular ?: $this->legal_regular;
        }

        $this->legal_regular = '/' . trim($this->legal_regular, '/') . '/';

        return $this->legal_regular;
    }

    /**
     * @param $value
     * @return string
     */
    public function encrypt($value)
    {
        if (empty($value)) {
            return '';
        }

        if ($this->legal_verify && !preg_match($this->legal_regular, $value)) {
            return '';
        }

        $value = $this->secret_key . '_' . $value;

        return sha1(md5($value));
    }

    /**
     * @param string $value
     * @param string|array $encrypt
     * @return bool
     */
    public function check($value, $encrypt)
    {
        if (empty($value)) {
            return false;
        }

        if (is_array($encrypt)) {
            $encrypt = isset($encrypt['password']) ? $encrypt['password'] : '';
        }

        if (empty($encrypt)) {
            return false;
        }


        if ($this->encrypt($value) === $encrypt) {
            return true;
        }

        return false;
    }
}