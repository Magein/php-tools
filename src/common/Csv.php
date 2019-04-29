<?php

namespace magein\php_tools\common;

use magein\php_tools\traits\Instance;

/**
 * 导出csv逻辑类
 * Class CsvLogic
 * @package app\common\core\logic\extra
 */
class Csv
{
    use Instance;

    /**
     * @var null|static 实例对象
     */
    protected static $instance = null;


    /**
     * 获取示例
     * @param array $options 实例配置
     * @return static
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) self::$instance = new self($options);

        return self::$instance;
    }

    /**
     * 获取导出的文件名称
     * @param null $fileName
     * @return null|string
     */
    private function getFileName($fileName = null)
    {
        if (empty($fileName)) {
            $fileName = date('YmdHi') . rand(1000, 9999);
        }

        if (!preg_match('/.csv/', $fileName)) {
            $fileName .= '.csv';
        }

        return $fileName;
    }

    /**
     * 校正表头跟数据主体的位置信息
     * @param $header
     * @param $data
     * @return array|bool
     */
    public function correcting($header, $data)
    {
        if (!is_array($header)) {
            return false;
        }

        $result = [];

        foreach ($data as $datum) {
            $temp = [];
            foreach ($header as $key => $item) {
                if (isset($datum[$key])) {
                    $temp[] = $datum[$key];
                }
            }
            $result[] = $temp;
        }

        return $result;
    }

    /**
     * @param array $header
     * @param array $formData
     * @param string $fileName
     * @return bool
     */
    public function export($header = [], $formData = [], $fileName = '')
    {
        if (empty($formData)) {
            return false;
        }

        if (is_array($header)) {
            $header = implode(',', $header);
        }

        $string = '';

        if ($header) {
            $string = $header . "\n";
        }

        foreach ($formData as $item) {
            if (is_array($item)) {
                $tmp = '';
                foreach ($item as $val) {
                    $tmp .= '"' . preg_replace('/(["])/', '"$1', $val) . '"' . ',';
                }
                $string .= trim($tmp, ',') . "\n";
            }
        }

        $fileName = $this->getFileName($fileName);

        header('Content-Type: text/csv; charset=utf-8');
        if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition:  attachment; filename="' . $fileName . '"');
        } else {
            if (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])) {
                header('Content-Disposition: attachment; filename*="utf8' . $fileName . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
            }
        }
        echo "\xEF\xBB\xBF" . $string;
        exit();
    }
}