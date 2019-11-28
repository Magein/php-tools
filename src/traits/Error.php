<?php

namespace magein\php_tools\traits;

trait Error
{
    /**
     * @var string
     */
    private $error = '';

    /**
     * @var int
     */
    private $code = 1;

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

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
     * @param integer $code
     * @return bool
     */
    public function setError($error = '出错啦~~', $code = 0)
    {
        $this->error = $error;
        $this->code = $code;

        // 如果code为1 则修改为0，自定义code则不进行修改
        if ($this->code == 1) {
            $this->code = 0;
        }

        return false;
    }
}