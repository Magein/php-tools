<?php

namespace magein\php_tools\common;


use magein\php_tools\traits\Instance;

class Password
{
    use Instance;

    /**
     * @param $value
     * @param null $prefix
     * @return string
     */
    public function encrypt($value, $prefix = null)
    {
        if ($prefix) {
            $value = $prefix . '_' . $value;
        }

        return sha1(md5($value));
    }

    /**
     * @param $value
     * @param $encrypt
     * @param null $prefix
     * @return bool
     */
    public function check($value, $encrypt, $prefix = null)
    {
        if (empty($value)) {
            return false;
        }

        if ($this->encrypt($value, $prefix) === $encrypt) {
            return true;
        }

        return false;
    }
}