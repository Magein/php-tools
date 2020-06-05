<?php


namespace magein\php_tools\common;

use magein\php_tools\traits\Error;
use magein\php_tools\traits\Instance;

class BaiDuMap
{
    use Error;

    use Instance;

    /**
     * @var string
     */
    private $apiUrl = 'http://api.map.baidu.com/';

    /**
     * @var string
     */
    private $ak = '';

    /**
     * @var string
     */
    private $output = 'json';

    /**
     * @var array
     */
    private $result = [];

    /**
     * 发送请求之前的回调
     * @var null
     */
    private $before = null;

    /**
     * 发送请求之后的回调
     * @var null
     */
    private $after = null;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize()
    {

    }

    /**
     * @param $before
     * @return $this
     */
    public function setBefore(callable $before)
    {
        $this->before = $before;

        return $this;
    }

    /**
     * @param $after
     * @return $this
     */
    public function setAfter(callable $after)
    {
        $this->after = $after;

        return $this;
    }

    /**
     * @return string
     */
    public function getAk(): string
    {
        if (empty($this->ak)) {
            $this->ak = $this->getConfig('ak');
        }

        return $this->ak;
    }

    /**
     * @param string $ak
     */
    public function setAk(string $ak): void
    {
        $this->ak = $ak;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output ?: 'json';
    }

    /**
     * @param string $output
     */
    public function setOutput(string $output): void
    {
        $this->output = $output;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param $key
     * @return mixed|string
     */
    private function getConfig($key)
    {
        $value = '';

        if (class_exists('think\Config')) {

            $key = 'bd_map.' . $key;

            $value = call_user_func_array('think\Config::get', [$key]);
        };

        return $value;
    }

    /**
     * @param $param
     * @return mixed
     */
    private function concatParam($param)
    {
        $param['ak'] = $this->getAk();
        $param['output'] = $this->getOutput();

        return $param;
    }

    /**
     * 发送请求
     * @param $url
     * @param $param
     * @return bool
     */
    public function request($url, $param)
    {
        if (!preg_match('/^http:/', $url)) {
            $url = $this->apiUrl . $url;
        }

        if ($this->before) {
            call_user_func($this->before, $url, $param);
        }

        $result = Curl::instance()->get($url, $this->concatParam($param));

        if ($this->after) {
            call_user_func($this->after, $url, $result, $param);
        }

        if (false === $result) {
            return $this->setError(Curl::instance()->getError());
        }

        $result = json_decode($result, true);
        if (isset($result['status']) && $result['status'] != 0) {
            return $this->setError($result['msg']);
        }

        $this->result = $result;


        return $result;
    }

    /**
     * 参考文档：http://lbsyun.baidu.com/index.php?title=webapi/guide/webservice-geocoding
     * @param $address
     * @return Location|bool
     */
    public function getLocationByAddress($address)
    {
        $this->request('geocoder/v2/', ['address' => $address]);

        if (isset($this->result['result']['location'])) {
            $location = $this->result['result']['location'];
            return new Location($location);
        }

        return false;
    }

    /**
     * @param array $result
     * @param bool $text
     * @return mixed|string
     */
    public function transLocationPrecision($result = [], $text = true)
    {
        if (empty($result)) {
            $result = $this->result['result'] ?? [];
        }

        $precise = 'low';
        if (isset($result['precise'])) {
            if ($result['precise'] == 1) {
                $precise = 'high';
            } elseif (in_array($result['level'], ['城市', '区县'])) {
                $precise = 'low';
            } else {
                $precise = 'common';
            }
        }

        $trans = [
            'low' => '低',
            'common' => '一般',
            'high' => '高'
        ];

        $desc = $trans[$precise] ?? '低';

        if ($text) {
            return $desc;
        }

        return [
            'level' => $precise,
            'text' => $desc,
        ];
    }
}