<?php

namespace magein\php_tools\think;

use magein\php_tools\common\UnixTime;
use traits\model\SoftDelete;

class Model extends \think\Model
{
    /**
     * 使用软删除
     */
    use SoftDelete;

    /**
     * @var string
     */
    protected $pk = 'id';

    /**
     * 自动写入时间
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 软删除字段
     * @var string
     */
    protected $deleteTime = 'delete_time';

    /**
     * 时间取出来后的格式，这里指代的是 create_time,update_time等tp能够识别的
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * 自读字段
     * @var array
     */
    protected $readonly = [
        'id',
        'create_time',
    ];

    /**
     * @param $value
     * @param $data
     * @return string
     */
    protected function setStartTimeAttr($value, $data)
    {
        $value = UnixTime::instance()->unix($value);

        return $value ?: '';
    }

    /**
     * @param $value
     * @return false|string
     */
    protected function getStartTimeAttr($value)
    {
        if ($value) {
            return date('Y-m-d H:i:s', $value);
        }

        return $value;
    }

    /**
     * @param $value
     * @param $data
     * @return string
     */
    protected function setEndTimeAttr($value, $data)
    {
        $value = UnixTime::instance()->unix($value);

        return $value ?: '';
    }

    /**
     * @param $value
     * @return false|string
     */
    protected function getEndTimeAttr($value)
    {
        if ($value) {
            return date('Y-m-d H:i:s', $value);
        }

        return $value;
    }
}