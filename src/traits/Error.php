<?php

namespace magein\php_tools\traits;

trait Error
{
    private $error = '';

    /**
     * @return string
     */
    public function getError()
    {
        if (is_object($this->error)) {

            if (method_exists($this->error, 'getError')) {
                return call_user_func_array([$this->error, 'getError'], []);
            }

        }

        return $this->error ?: '发生错误，请重试';
    }

    /**
     * @param string $error
     * @return bool
     */
    public function setError($error)
    {
        $this->error = $error;

        return false;
    }
}