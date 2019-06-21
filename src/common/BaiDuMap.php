<?php


namespace magein\php_tools\common;

use magein\php_tools\traits\Error;

class BaiDuMap
{
    use Error;

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
    private function request($url, $param)
    {
        if (!preg_match('/^http:/', $url)) {
            $url = $this->apiUrl . $url;
        }

        $result = Curl::instance()->get($url, $this->concatParam($param));

        if (false === $result) {
            return $this->setError(Curl::instance()->getError());
        }

        $result = json_decode($result, true);

        if (isset($result['status']) && $result['status'] != 0) {
            return $this->setError($result['message']);
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
}