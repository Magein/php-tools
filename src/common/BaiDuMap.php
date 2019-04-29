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
    private $ak = 'qZhnLdXzBqW2ksrpAyabuxcdNXYxMSvs';

    /**
     * @var string
     */
    private $output = 'json';

    /**
     * @return string
     */
    public function getAk(): string
    {
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
        return $this->output;
    }

    /**
     * @param string $output
     */
    public function setOutput(string $output): void
    {
        $this->output = $output;
    }

    /**
     * 参考文档：http://lbsyun.baidu.com/index.php?title=webapi/guide/webservice-geocoding
     * @param $address
     * @return array|bool
     */
    public function getLngAndLatByAddress($address)
    {
        $apiUrl = $this->apiUrl . 'geocoder/v2/';

        $param = [
            'address' => $address,
            'output' => $this->output,
            'ak' => $this->ak
        ];

        $result = Curl::instance()->get($apiUrl, $param);

        if (false === $result) {
            $this->setError(Curl::instance()->getError());
            return false;
        }

        $result = json_decode($result, true);

        if (isset($result['status']) && $result['status'] != 0) {
            $this->setError($result['msg']);
            return false;
        }

        $location = $result['result']['location'];

        return ['lat' => $location['lat'], 'lng' => $location['lng']];
    }


}