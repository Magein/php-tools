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
    private $savePath = './uploads/';

    /**
     * @var string
     */
    private $store = 'local';

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
     * 文件上传，
     * @param null $file 支持传递一个file对象，或者字符串，
     * @param int $size 最大字节数量 默认1M
     * @param array|string $ext
     * @return array|bool
     */
    public function file($file = null, $size = 1048576, $ext = [])
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

        if ($ext && is_string($ext)) {
            $ext = explode('|', $ext);
        }

        $rule['size'] = $size;

        if ($ext) {
            $rule['ext'] = $ext;
        }

        $info = $file->validate($rule)->move($this->savePath);

        /**
         * 这里要区别于保存路径和展示路径
         *
         * 保存路径使用/ 可能会保存到项目目录外，取决您的配置参数
         *
         * 在展示的时候基于项目根目录展示，
         */

        if ($info) {
            return [
                'url' => trim($this->savePath . $info->getSaveName(), '.'),
                'save_name' => $info->getSaveName(),
                'file_name' => $info->getFilename(),
            ];
        }

        $this->setError($file->getError());

        return false;
    }

    /**
     * 上传图片
     * @param null $file
     * @param int $size
     * @return bool
     */
    public function image($file = null, $size = 17240)
    {
        return $this->file($file, $size, ['jpg', 'gif', 'gif', 'jpeg']);
    }
}