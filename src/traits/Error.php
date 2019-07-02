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

        return $this->error ?: '';
    }

    /**
     * @param string $error
     * @return bool
     */
    public function setError($error = '出错啦~~')
    {
        $this->error = $error;

        return false;
    }
}