<?php

namespace magein\php_tools\extra;

use Endroid\QrCode\QrCode as EndroidQrCode;
use magein\php_tools\traits\Error;
use magein\php_tools\traits\Instance;

/**
 * 二维码类
 * Class QrCodeLogic
 * @package app\common\core\logic\extra
 */
class QrCode
{
    use Instance;
    use Error;

    /**
     * @param $url
     * @return bool|string
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionFailedException
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionUnknownException
     */
    public function base64($url)
    {
        if (empty($url)) {
            $this->setError('请输入二维码内容');
            return false;
        }

        $qrCode = new EndroidQrCode($url);

        return base64_encode($qrCode->get());
    }

    /**
     * @param $url
     * @return bool
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionFailedException
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionUnknownException
     */
    public function output($url)
    {
        if (empty($url)) {
            $this->setError('请输入内容');
            return false;
        }

        $qrCode = new EndroidQrCode($url);

        ob_clean();
        header('Content-Type: ' . $qrCode->getContentType());
        $qrCode->render();
        exit();
    }
}