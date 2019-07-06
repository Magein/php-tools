<?php

namespace magein\php_tools\think;

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
class ApiBehavior
{

    use Instance;

    /**
     * @param $request
     * @return bool
     */
    public function run(&$request)
    {
        return ApiBehavior::instance()->login($request);
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
            $config = 'api_request_skip_login_check';
        }

        if (is_string($config)) {
            $config = config($config);
        }

        if (empty($config)) {
            return false;
        }

        if ($request) {
            $controller = $request->controller();
            $action = $request->action();
        } else {
            $controller = Request::instance()->controller();
            $action = Request::instance()->action();
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
     * 验证票据
     * @param Request|null $request
     * @param string $sign
     * @return string
     */
    public function ticket(Request $request = null, $sign = 'ticket')
    {
        if (empty($request)) {
            $request = Request::instance();
        }

        $ticket = $request->header($sign);

        if (empty($ticket)) {
            throw new HttpException(1001, '无效的请求信息');
        }

        try {
            Session::init(['id' => $ticket]);
        } catch (Exception $exception) {
            throw new HttpException(1000, '服务器内部出错误');
        }

        if ($ticket !== Session::get('session_ticket')) {
            throw new HttpException(1002, '无效的请求信息');
        }

        return $ticket;
    }

    /**
     * 用户登录
     * @param Request $request
     * @param ApiLogin|null $login
     * @return bool
     */
    public function login(Request $request, ApiLogin $login = null)
    {
        if ($this->check($request)) {
            return true;
        }

        $this->ticket($request);

        if ($login === null) {
            $login = new ApiLogin();
        }

        $id = $login->id();

        if (empty($id)) {
            throw new HttpException(1010, '请先登录');
        }

        return true;
    }
}