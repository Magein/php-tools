<?php

namespace magein\php_tools\extra;

use magein\php_tools\traits\Error;
use magein\php_tools\traits\Instance;
use think\File;
use think\Request;

class Upload
{
    use Error;
    use Instance;

    /**
     * 保存路径
     * @var string
     */
    private $savePath = '/uploads/';

    /**
     * @var string
     */
    private $store = 'local';


    /**
     * 兼用web接口访问
     *
     * 使用场景：为小程序，app提供接口的时候，图片如果是基于项目目录下回导致访问失败
     *
     * @var bool
     */
    private $useWebUrl = true;

    /**
     * @var string
     */
    private $host = '';

    /**
     * @param $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param bool $use
     */
    public function setUseWebUrl(bool $use = true)
    {
        $this->useWebUrl = $use ? true : false;
    }

    /**
     * @param string $savePath
     * @return $this
     */
    public function setSavePath(string $savePath)
    {
        $this->savePath = $savePath;

        return $this;
    }

    /**
     * 设置驱动方式
     * @param string $store
     * @return $this
     */
    public function setStore(string $store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * 上传图片
     * $file 为空的时候，自动接收field作为接收文件的名称
     * $file 为字符串的时候，作为接收文件的名称
     * $file 为File对象的时候，直接使用
     * @param null|string|File $file
     * @param int $size 单位是kb
     * @param array $ext
     * @return array|bool
     */
    public function image($file = null, $size = 1024, $ext = ['jpeg', 'png', 'gif', 'jpg'])
    {
        if ($file === null) {
            $field = Request::instance()->param('field', 'file');
            $file = Request::instance()->file($field);
        }

        if (!$file instanceof File) {
            $file = Request::instance()->file($file);
        }

        if (empty($file)) {
            $this->setError('请选择一张图片');
            return false;
        }

        $info = $file->validate(
            [
                'size' => $size * 1024,
                'ext' => $ext,
            ]
        )->move('./' . $this->savePath);

        /**
         * 这里要区别于保存路径和展示路径
         *
         * 保存路径使用/ 可能会保存到项目目录外，取决您的配置参数
         *
         * 在展示的时候基于项目根目录展示，
         */

        if ($info) {
            return [
                'url' => $this->getUrl($info->getSaveName()),
                'save_name' => $info->getSaveName(),
                'file_name' => $info->getFilename(),
            ];
        }

        $this->setError($file->getError());

        return false;
    }

    /**
     * 获取访问图片链接
     * @param $name
     * @return string
     */
    private function getUrl($name)
    {
        $url = $this->savePath . $name;
        if ($this->store != 'local') {
            return $url;
        }

        if ($this->useWebUrl) {

            if (empty($this->host)) {
                $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
            } else {
                $host = $this->host;
            }

            $host = trim($host, '/');

            if (!preg_match('/^http/', $host)) {
                $host = 'http://' . $host;
            }

            $url = $host . '/' . $url;
        }

        return $url;
    }
}