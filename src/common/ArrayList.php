<?php

namespace magein\php_tools\common;

/**
 * Class ArrayList
 * @package app\common\core\logic\extend
 */
class ArrayList
{
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
     * 要处理的数据
     * @var array
     */
    private $data = [];
    /**
     * 一维数组中要保留的元素字段，一般用在数据列表中的字段过滤，那些可以传递给前段，那些字段比较敏感，需要过滤掉
     * @var array
     */
    private $keepFields = [];
    /**
     * 要收集的一维数组的元素，一般用在数据列表中，获取用来关联的字段，然后去查询关联的数组
     * @var array
     */
    private $collectFields = [];
    /**
     * $collectFields 变量的结果，通过变量中的值获取信息
     *
     * 如：传递了 $collectFields=['uid','rid']  则可以通过 $fields['uid'], $fields['rid'] 获取 uid  rid的结果数组
     *
     * @var array
     */
    private $collectResult = [];
    /**
     * 重新指定键值
     * @var string
     */
    private $key = '';
    /**
     * 重新生成的数组
     * @var array
     */
    private $result = [];

    /**
     * ArrayList constructor.
     * @param array $data
     * @param array $keepFields
     * @param array $collectFields
     * @param string $key
     */
    public function __construct(array $data = [], array $collectFields = [], array $keepFields = [], $key = '')
    {
        $this->data = $data;
        $this->collectFields = $collectFields;
        $this->keepFields = $keepFields;
        $this->key = $key;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        // 防止同一个实例对不同的数据产生的干扰
        $this->result = [];
        $this->collectResult = [];
        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setKeepFields(array $fields)
    {
        $this->keepFields = $fields;
        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setCollectFields(array $fields)
    {
        $this->collectFields = $fields;
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return array
     */
    public function getCollectFields()
    {
        return $this->collectResult;
    }

    /**
     * 收集二维数组中元素（一维数组中的一个字段），组成一个新的数组
     *
     * 传递：
     *
     * $data=[
     *      ['id'=>1,'name'=>'x']
     *      ['id'=>2,'name'=>'xx']
     *      ['id'=>3,'name'=>'xx']
     *      .....
     * ]
     *
     * $key='id'
     *
     * 返回：
     *     [
     *          1=>['id'=>1,'name'=>'x'],
     *          2=>['id'=>2,'name'=>'xx']
     *          3=>['id'=>2,'name'=>'xx']
     *          .....
     *     ]
     *
     * ================================================
     *
     * 传递：
     *
     * $data=[
     *      ['id'=>1,'name'=>'x']
     *      ['id'=>2,'name'=>'xx']
     *      ['id'=>3,'name'=>'xx']
     *      .....
     * ]
     *
     *
     * $key=id
     *
     * $collectFields=['id','name']
     *
     * 返回：
     *   [
     *       1=>['id'=>1,'name'=>'x'],
     *       2=>['id'=>2,'name'=>'xx']
     *       3=>['id'=>2,'name'=>'xx']
     *          .....
     *   ]
     *
     * 通过 $this->getCollectFields() 得到;
     *    [
     *       'id'=>[1,2,3]
     *      'name'=>[x,xx,xx]
     *    ];
     *
     * =============================================
     *
     * 传递：
     * $data=[
     *      ['id'=>1,'name'=>'x','sex'=>1]
     *      ['id'=>2,'name'=>'xx','sex'=>2]
     *      ['id'=>3,'name'=>'xx','sex'=>0]
     *      .....
     * ]
     *
     * $keepFields=['id','name']
     *
     * 返回:
     *  [
     *      ['id'=>1,'name'=>'x']
     *      ['id'=>2,'name'=>'xx']
     *      ['id'=>3,'name'=>'xx']
     *  ]
     *
     * 同时指定了：
     *
     * $collectFields=['id','sex']
     *
     * 通过 $this->getCollectFields() 得到;
     *   [
     *          'id'=>[1,2,3]
     *          'sex'=>[1,2,0]
     *    ];
     *
     *
     *
     * @return array
     */
    public function getData()
    {
        if (empty($this->data)) {
            return $this->data;
        }
        foreach ($this->data as $key => $record) {
            if (is_array($record)) {
                // 处理需要收集字段的信息
                $this->collectFields($record);
                // 处理只保留的字段信息以及是否生成一个指定键的数组
                $this->keepFields($record, $key);
            }
        }
        return $this->result;
    }

    /**
     * 收集字段值，组成一个新的数组，这里不保证数组的长度，取决于传递的数组（一维数组）
     * @param array $record
     */
    private function collectFields(array $record)
    {
        if ($this->collectFields) {
            foreach ($this->collectFields as $field) {
                if (isset($record[$field])) {
                    $this->collectResult[$field][] = $record[$field];
                }
            }
        }
    }

    /**
     * 1. 如果指定了 需要保留的字段信息，则进行过滤
     * 2. 如过指定了使用数组中的某个键的值为新数组的键，则重新生成一个以字段值为键的数组
     * @param $record
     * @param string $key
     */
    private function keepFields($record, $key)
    {
        if ($this->key && isset($record[$this->key]) && $record[$this->key]) {
            $key = $record[$this->key];
        }
        if ($this->keepFields) {
            foreach ($this->keepFields as $field) {
                if (isset($record[$field])) {
                    $this->result[$key][$field] = $record[$field];
                }
            }
        } else {
            $this->result[$key] = $record;
        }
    }
}