<?php

namespace magein\php_tools\common;

use magein\php_tools\traits\Instance;

/**
 * Class RandString
 * @package app\common\core\logic\extend
 */
class RandString
{

    use Instance;

    /**
     * 大写
     */
    const TYPE_UPPER = 'upper';

    /**
     * 小写
     */
    const TYPE_LOWER = 'lower';

    /**
     * 数字
     */
    const TYPE_NUM = 'number';

    /**
     * 大写+小写
     */
    const TYPE_UPPER_LOWER = 'upper_lower';

    /**
     * 大写+数字
     */
    const  TYPE_UPPER_NUM = 'upper_num';

    /**
     * 小写+数字
     */
    const TYPE_LOWER_NUM = 'lower_num';

    /**
     * 混合
     */
    const TYPE_MIXED = 'mixed';

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
     * 生成随机字符串
     * @param int $length 长度
     * @param string $type 类型
     * @param bool $filter 是否过滤掉 o z 0 2 这些容易混淆的字符
     * @return string
     */
    public function make($length = 32, $type = self::TYPE_MIXED, $filter = false)
    {
        $lower_letter = 'abcdefghijklmnopqrstuvwxyz';
        $upper_letter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $number = '0123456789';

        if ($filter) {
            $lower_letter = preg_replace('/o|z/', '', $lower_letter);
            $upper_letter = preg_replace('/O|Z/', '', $lower_letter);
            $number = preg_replace('/0|2/', '', $lower_letter);
        }

        switch ($type) {
            case self::TYPE_UPPER:
                $chars = $upper_letter;
                break;
            case self::TYPE_LOWER:
                $chars = $lower_letter;
                break;
            case self::TYPE_NUM:
                $chars = $number;
                break;
            case self::TYPE_UPPER_LOWER:
                $chars = $upper_letter . $lower_letter;
                break;
            case self::TYPE_UPPER_NUM:
                $chars = $upper_letter . $number;
                break;
            case self::TYPE_LOWER_NUM:
                $chars = $lower_letter . $number;
                break;
            default :
                $chars = $lower_letter . $upper_letter . $number;
                break;
        }

        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $string;
    }
}