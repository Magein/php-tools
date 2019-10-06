<?php

namespace magein\php_tools\common;

use magein\php_tools\traits\Error;
use magein\php_tools\traits\Instance;

class Curl
{
    use Error;
    use Instance;
    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var null
     */
    private $result = null;

    /**
     * @var array
     */
    private $options = [
        CURLOPT_TIMEOUT => 30,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ];

    /**
     * @param $headers
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param bool $dataJson
     * @return mixed|null
     */
    public function getResult($dataJson = true)
    {
        if ($this->result !== false && $dataJson) {
            return json_decode($this->result, true);
        }

        return $this->result;
    }

    /**
     * @param $url
     * @return bool|mixed
     */
    public function init($url)
    {
        /* 初始化并执行curl请求 */
        $ch = curl_init($url);
        curl_setopt_array($ch, $this->options);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            return $this->setError('curl请求错误：' . $error);
        }

        return $data;
    }

    /**
     * @param $url
     * @param array|string $params
     * @return bool|mixed
     */
    public function post($url, $params = [])
    {
        $this->options[CURLOPT_POST] = 1;
        $this->options[CURLOPT_POSTFIELDS] = $params;

        return $this->init($url);
    }

    /**
     * @param $url
     * @param array $params
     * @return bool|mixed
     */
    public function get($url, $params = [])
    {
        if ($params) {
            $url = $url . '?' . http_build_query($params);
        }

        return $this->init($url);
    }
}