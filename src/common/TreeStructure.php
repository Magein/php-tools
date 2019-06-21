<?php

namespace magein\php_tools\common;

use magein\php_tools\traits\Instance;

/**
 * 处理树结构
 * Class TreeStructure
 * @package magein\php_tools\common
 */
class TreeStructure
{
    use Instance;

    /**
     * @var array
     */
    private $parent = [];

    /**
     * 主键字段
     * @var string
     */
    private $primary = 'id';

    /**
     * 父级字段
     * @var string
     */
    private $parent_id = 'pid';

    /**
     * 用来展示层级关系的字段数据
     * @var string
     */
    private $title = 'title';

    /**
     * 最后一层的值
     * @var int
     */
    private $level = 0;

    /**
     * 用来展示层级关系的前缀
     * @var string
     */
    private $sign = '|--';

    /**
     * @param string $primary
     * @return $this
     */
    public function setPrimary(string $primary)
    {
        $this->primary = $primary ?: $this->primary;

        return $this;
    }

    /**
     * @param string $parent_id
     * @return $this
     */
    public function setParentId(string $parent_id)
    {
        $this->parent_id = $parent_id ?: $this->parent_id;

        return $this;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $sign
     */
    public function setSign(string $sign)
    {
        $this->sign = $sign;
    }

    /**
     * @return array
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * 获取树
     * @param $records
     * @return array|bool
     */
    public function tree($records)
    {
        if (empty($records)) {
            return false;
        }

        /**
         * 清除parent信息，防止连续调用产生的污染
         */
        $this->parent = [];

        $parent_id = $this->parent_id;
        $primary = $this->primary;

        $result = [];

        foreach ($records as $key => $item) {
            if (isset($records[$item[$parent_id]])) {
                $records[$item[$parent_id]]['child'][] = &$records[$key];
            } else {
                if ($primary && $item[$primary]) {
                    $result[$item[$primary]] = &$records[$key];
                } else {
                    $result[] = &$records[$key];
                }

                $this->parent[] = $records[$key];
            }
        }

        return $result;
    }

    /**
     * 获取层
     * @param array $records 要处理的数据
     * @param callable $callback 成绩关系描述
     * @param int $limit 限制处理的层级关系
     * @return array
     */
    public function floor($records, $callback = null, $limit = 3)
    {
        /**
         * 清除parent信息，防止连续调用产生的污染
         */
        $this->parent = [];

        $tree = function ($records, $pid = 0, $level = 1) use (&$tree, $callback, $limit) {

            static $result = [];

            if ($level > $limit) {
                return $result;
            }

            foreach ($records as $key => $val) {
                if ($val[$this->parent_id] == 0) {
                    $this->parent[] = $val;
                }
                if ($val[$this->parent_id] == $pid) {
                    $flg = str_repeat($this->sign, $level - 1);
                    $val[$this->title] = $flg . $val[$this->title];
                    $val['level'] = $level;
                    $this->level = $level;
                    if (is_callable($callback)) {
                        $val = call_user_func($callback, $val, $result);
                    }

                    $result[$val['id']] = $val;
                    $tree($records, $val['id'], $level + 1);
                }
            }
            return $result;
        };

        return $tree($records);
    }
}