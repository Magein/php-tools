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
     * @param $url
     * @param bool $dataJson
     * @return bool|mixed
     */
    public function init($url, $dataJson = false)
    {
        /* 初始化并执行curl请求 */
        $ch = curl_init($url);
        curl_setopt_array($ch, $this->options);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            $this->setError('curl请求错误：' . $error);
            return false;
        }

        if ($dataJson) {
            return json_decode($data, true);
        }

        return $data;
    }

    /**
     * @param $url
     * @param array $params
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
        $url = $url . '?' . http_build_query($params);

        return $this->init($url);
    }
}