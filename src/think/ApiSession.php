<?php

namespace magein\php_tools\think;

use magein\php_tools\common\RandString;
use magein\php_tools\traits\Instance;
use think\Exception;
use think\exception\HttpException;
use think\Request;
use think\Session;

/**
 * 接口行为验证
 * Class ApiBehavior
 * @package app\common
 */
class ApiSession
{
    use Instance;

    /**
     * @var string
     */
    protected $key = 'clEOH3e9IwB$SY2r';

    /**
     * @return string
     */
    protected function getKey()
    {
        return $this->key;
    }

    /**
     * @param $param
     * @return string
     */
    public static function id($id = '')
    {
        if (empty($id)) {
            $result = self::instance()->check();
            if (isset($result['ticket']) && $result['ticket']) {
                return $result['ticket'];
            }
        }
        return $id;
    }

    /**
     * 生成session_ticket
     * @param string $prefix
     * @param string $type
     * @param int $expre_time
     * @return string
     */
    public function make($prefix = 'session_ticket', $type = 'sha1', $expre_time = 604800)
    {
        $ticket = substr($prefix . uniqid($prefix . time() . rand(10000, 99999)), 0, 32);

        $data['timestamp'] = time();
        $data['type'] = $type;
        $data['ticket'] = $ticket;
        $data['expire_time'] = time() + $expre_time;

        $sign = $this->sign($data);

        $data = $data = preg_replace('/\./', '', $this->base64Encode($data));

        return $data . '.' . $sign;
    }

    /**
     * @param null $session_ticket
     * @return bool|array
     */
    public function check($session_ticket = null)
    {
        $session_ticket = $session_ticket ?: Request::instance()->header('X-Request-Session-Ticket');

        if (empty($session_ticket)) {
            return false;
        }

        $session_ticket = explode('.', $session_ticket);

        if (count($session_ticket) != 2) {
            return false;
        }

        $data = $this->base64Decode($session_ticket[0]);

        parse_str($data, $param);

        if ($param['expire_time'] < time()) {
            return false;
        }

        // 对数据进行加密
        $sign = $this->sign($param);
        if ($sign != $session_ticket[1]) {
            return false;
        }

        return $param;
    }

    /**
     * @param string $input
     * @return mixed
     */
    private function base64Encode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * @param string $input
     * @return bool|string
     */
    private function base64Decode(string $input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $add_length = 4 - $remainder;
            $input .= str_repeat('=', $add_length);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * @param $param
     * @return string
     */
    private function sign(&$param)
    {
        if (empty($param)) {
            return false;
        }

        ksort($param);

        $type = isset($param['type']) ? $param['type'] : 'sha1';

        $param = http_build_query($param);

        if ($type === 'md5') {
            $sign = md5($param . '&' . $this->getKey());
        } else {
            $sign = sha1($param . '&' . $this->getKey());
        }

        return $sign;
    }
}