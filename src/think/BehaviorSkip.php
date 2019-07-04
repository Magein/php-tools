<?php

namespace magein\php_tools\think;


use think\Request;

class BehaviorSkip
{
    /**
     *
     * @param $config
     * @param Request|null $request 这个参数是为了兼容手动修改了请求信息中的值，可以不传递
     * @return bool
     */
    public static function check($config, Request $request = null)
    {
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
}