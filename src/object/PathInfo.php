<?php

namespace magein\php_tools\object;

class PathInfo
{
    private $info = [];

    public function __construct($path)
    {
        $this->info = pathinfo($path);
    }

    /**
     * 获取路径信息
     * @return mixed
     */
    public function getDirName()
    {
        return $this->info['dirname'];
    }

    /**
     * 获取文件名称 不包含路径，包含后缀
     * @return mixed
     */
    public function getBaseName()
    {
        return $this->info['basename'];
    }

    /**
     * 获取扩展名称
     * @return mixed
     */
    public function getExtension()
    {
        return $this->info['extension'];
    }

    /**
     * 获取文件名称 不包含后缀
     * @return mixed
     */
    public function getFileName()
    {
        return $this->info['filename'];
    }
}