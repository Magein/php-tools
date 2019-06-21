<?php

namespace magein\php_tools\common;

/**
 * 此接口为了规范经纬度，不同服务平台返回的经纬度值可以通过此此类自动区分以及获取
 * Class Location
 * @package magein\php_tools\common
 */
class Location
{
    private $longitude = '';

    private $latitude = '';

    public function __construct($location = null)
    {
        $this->parse($location);
    }

    /**
     * 自动规整精度为的值
     * 经度：0-180
     * 纬度：0-90
     * @param object|array|string $location
     * @return bool
     */
    private function parse($location)
    {
        if (is_object($location)) {
            $location = json_decode($location, true);
        }

        if (!is_array($location)) {
            $location = explode(',', $location);
        }

        if (count($location) == 2) {
            $location = array_values($location);
            $longitude = $location[0];
            $latitude = $location[1];

            if (0 < $latitude && $latitude <= 90) {

                $this->latitude = $latitude;
                $this->longitude = $longitude;
            } else {
                $this->latitude = $longitude;
                $this->longitude = $latitude;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }
}