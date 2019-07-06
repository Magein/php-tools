<?php

namespace magein\php_tools\think;

use magein\php_tools\common\RandString;
use think\Session;

class ApiLogin
{
    /**
     * 登录数据
     */
    const USER_AUTH = 'user_auth_data';

    /**
     * 加密的登录数据
     */
    const USER_AUTH_SIGN = 'user_auth_sign';

    /**
     * 用户登录的有效期
     */
    const USER_LOGIN_EXPIRE_TIME = 'user_login_expire_time';

    /**
     * @param null $data
     * @param int $expire_time
     * @return null
     */
    public function set($data = null, $expire_time = 172800)
    {
        if (isset($data['ticket']) && $data['ticket']) {
            $ticket = $data['ticket'];
        } else {
            // 生成ticket
            if (isset($data['id']) && $data['id']) {
                $ticket = $data['id'] . RandString::instance()->make(48);
            } else {
                $ticket = RandString::instance()->make(16) . md5('Y-m-d h:i:s');
            }

            $ticket = sha1(md5($ticket));
        }

        $ticket = ApiSession::init($ticket);

        if (empty($data)) {
            Session::set(self::USER_AUTH, null);
            return true;
        }

        // 用户登录数据
        Session::set(self::USER_AUTH, $data);
        Session::set('session_ticket', $ticket);

        if ($data) {
            // 用户登录标识
            Session::set(self::USER_AUTH_SIGN, $data ? $this->dataAuthSign($data) : null);
            // 用户登录有效时间
            Session::set(self::USER_LOGIN_EXPIRE_TIME, time() + $expire_time);
        }

        $data['ticket'] = $ticket;

        return $data;
    }

    /**
     * @param null $key
     * @return bool|mixed|string
     */
    public function get($key = null)
    {
        $user_auth = Session::get(self::USER_AUTH);
        $user_auth_sign = Session::get(self::USER_AUTH_SIGN);

        if (empty($user_auth) || empty($user_auth_sign) || $this->dataAuthSign($user_auth) !== $user_auth_sign) {
            return false;
        }

        $expire_time = Session::get(self::USER_LOGIN_EXPIRE_TIME);

        // 登录时间已经过期
        if ($expire_time < time()) {
            $this->set();
            return false;
        }

        if ($key) {
            return isset($user_auth[$key]) ? $user_auth[$key] : '';
        }

        return $user_auth;
    }

    /**
     * 获取登录用户的id
     * @return bool|mixed|string
     */
    public function id()
    {
        return $this->get('id');
    }

    private function dataAuthSign($data)
    {
        // 数据类型检测
        if (!is_array($data)) {
            $data = (array)$data;
        }

        // 排序
        ksort($data);
        // url编码并生成query字符串
        $code = http_build_query($data);
        // 生成签名
        $sign = sha1($code);
        return $sign;
    }
}