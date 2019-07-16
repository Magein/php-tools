<?php

namespace magein\php_tools\think;

use magein\php_tools\traits\Instance;
use think\Config;
use think\exception\HttpException;
use think\Request;

/**
 * 接口行为验证
 * Class ApiBehavior
 * @package app\common
 */
class ApiBehavior
{

    use Instance;

    /**
     * @param $request
     * @return bool
     */
    public function run(&$request)
    {
        // 验证来源
        $this->request($request);
        // 验证session
        $this->session($request);

        $this->authorization($request);

        return true;
    }

    /**
     * 请求接口验证
     * @param Request|null $request
     * @param string $key
     * @param string $request_id
     * @return string
     */
    public function request(Request $request = null, $key = '', $request_id = 'X-Request-ID')
    {
        if (empty($request)) {
            $request = Request::instance();
        }

        $request_id = $request->header($request_id);

        $decrypt = function ($request_id, $key) {

            if (!is_array($request_id)) {
                $request_id = json_decode($request_id, true);
            }

            if (empty($request_id)) {
                return false;
            }

            /**
             * 检测request_id字段信息
             * @param $request_id
             * @return bool
             */
            $check_field = function ($request_id) {
                $field = [
                    'nostr',
                    'timestamp',
                    'sign',
                ];
                foreach ($field as $item) {
                    if (!isset($request_id[$item]) || empty($request_id[$item])) {
                        return false;
                    }
                }
                return true;
            };

            if (!$check_field($request_id)) {
                return false;
            }

            $sign = $request_id['sign'];
            $nostr = $request_id['nostr'];
            $timestamp = $request_id['timestamp'];

            /**
             * 如果请求时间间隔大于60秒，则请求失效
             *
             * 请注意：前段传递的时间戳带毫秒所以除以1000即可
             */
            $expire_time = Config::get('X-Request-ID.expire_time') ?: 60;
            if (time() - floor($timestamp / 1000) > $expire_time) {
                return false;
            }

            if ($sign == md5($key . $nostr . $timestamp)) {
                return true;
            }
            return false;
        };

        if (empty($key)) {
            $key = Config::get('X-Request-ID.key') ?: 'request_ticket_key';
        }

        if (!$decrypt($request_id, $key)) {
            throw new HttpException(1001, 'x-request-id错误');
        }

        return true;
    }

    /**
     * 用于检测那些是跳过登录验证数据
     * @param Request|null $request 这个参数是为了兼容手动修改了请求信息中的值，可以不传递
     * @param $config
     * @return bool
     */
    public function check(Request $request = null, $config = '')
    {
        if (empty($config)) {
            $config = 'authorize.skip';
        }

        if (is_string($config)) {
            $config = config($config);
        }

        if (empty($config)) {
            return false;
        }

        if ($request) {
            $controller = $request->controller();
            $action = $request->action(true);
        } else {
            $controller = Request::instance()->controller();
            $action = Request::instance()->action(true);
        }

        $check = function ($controller, $action = '*') use ($config) {

            $path = $controller . '/' . $action;

            if (in_array($path, $config)) {
                return true;
            }

            return false;
        };

        if ($check($controller)) {
            return true;
        }

        if ($check($controller, $action)) {
            return true;
        }

        return false;
    }

    /**
     * 验证用户授权信息是否正常
     * 这里仅仅只验证header中的授权信息，不验证用户是否异常，
     * 为了安全起见，应该在验证通过后，验证用户状态，网站性质不同，验证状态不同，关于验证用户状态逻辑请自行封装
     * 登录后，定义一个常量，用户后续获取用户的唯一标识
     * @param Request $request
     * @param ApiLogin|null $login
     * @return bool
     */
    public function authorization(Request $request, ApiLogin $login = null)
    {
        if ($this->check($request)) {
            return true;
        }

        $class = new ApiLogin();
        if ($login === null) {
            $namespace = 'app\\' . $request->module() . '\logic\LoginLogic';
            if (class_exists($namespace)) {
                $class = new $namespace();
            }
        }

        $record = [];
        if ($class && $class instanceof ApiLogin) {
            $record = $class->verifyToken();
        }

        // 验证授权信息中是否包含user_id(用户唯一标识)
        if (empty($record) || empty($record['user_id'])) {
            throw new HttpException(1010, '请先登录');
        }

        // 验证用户账号信息是否异常
        defined('API_USER_LOGIN_ID') or define('API_USER_LOGIN_ID', $record['user_id']);

        return $record;
    }

    /**
     * @param Request $request
     * @return array|bool
     */
    public function session(Request $request)
    {
        if (ApiSession::instance()->check($request)) {
            return true;
        }
        throw new HttpException(1002, 'x-request-session-ticket错误');
    }
}