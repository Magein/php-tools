<?php

namespace magein\php_tools\think;

use think\Response;
use traits\think\Instance;

class ApiReturn
{
    use Instance;

    /**
     * 约定条件
     * 0 或者 '0' 则不处理
     * false 、'' 、[]则全部处理成null
     * @param $data
     * @return null
     */
    private function formatData($data)
    {
        if (is_int($data) || $data === '0') {
            return $data;
        }

        return $data ? $data : null;
    }

    /**
     * @param int $code
     * @param string $message
     * @param $data
     * @return \think\response\Json
     */
    public function create($code, $message = '', $data = null)
    {
        $data = [
            'code' => $code,
            'msg' => $message,
            'data' => self::formatData($data)
        ];

        return Response::create($data, 'json', 200);
    }

    /**
     * @param null $data
     * @param string $message
     * @return \think\response\Json
     */
    public function success($data = null, $message = '')
    {
        $data = [
            'code' => 1,
            'msg' => $message,
            'data' => self::formatData($data)
        ];

        return Response::create($data, 'json', 200);
    }

    /**
     * @param string|object $message
     * @param null $data
     * @return \think\response\Json
     */
    public function error($message = '', $data = null)
    {
        if (is_object($message)) {
            $method = 'getError';
            if (method_exists($message, 'getError')) {
                $message = $message->$method();
            }
        }

        $data = [
            'code' => 0,
            'msg' => $message,
            'data' => self::formatData($data)
        ];

        return Response::create($data, 'json', 200);
    }
}