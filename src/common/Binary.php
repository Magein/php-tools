<?php

namespace magein\php_tools\common;

use magein\php_tools\traits\Instance;

class Binary
{
    use Instance;
    /**
     * @var null|static 实例对象
     */
    protected static $instance = null;

    /**
     * @var array
     */
    private $scope = [];

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
     * 设置作用域
     * @param array $scope
     * @return $this
     */
    public function setScope($scope = [])
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     *
     *  [1,2] 转化为 3
     *  [1,4] 转化为 5
     *  [2,4] 转化为 6
     *  [1,2,4] 转化为 7
     *
     *
     * @param array $value
     * @return int|mixed
     */
    public function orOperation($value = [])
    {
        $role = 1;

        if (is_array($value)) {
            $role = array_reduce($value, function ($string, $item) {
                return $string | $item;
            });
        };

        return $role;
    }

    /**
     * 7 转化为 [1,2,4]
     * 6 转化为 [2,4]
     * 5 转化为 [1,4]
     * @param $value
     * @return mixed
     */
    public function andOperation($value)
    {
        $result = [];

        $scope = $this->getScope();

        if (empty($scope)) {
            return $result;
        }

        foreach ($scope as $key => $vo) {
            if ($key & $value) {
                $result[] = $key;
            }
        }

        return $result;
    }

    private function getCombinations($str = '', &$comb = array())
    {
        if (trim($str) == '' || !$str) {
            return false;
        }
        if (strlen($str) <= 1) {
            $comb[] = $str;
        } else {
            $str_first = $str[0];
            $comb_temp = $this->getCombinations(substr($str, 1), $comb);
            $comb[] = $str_first;
            foreach ($comb_temp as $k => $v) {
                $comb[] = $str_first . $v;
            }
        }
        return $comb;
    }

    /**
     * 作用域为 1 的时候
     *
     * 值：
     *
     *  1 => 1
     *  1 | 2 => 3
     *  1 | 4 => 5
     *  1 | 2 | 4 => 7
     *
     * 1 => [ 1 , 3 , 5 , 7]
     * 2 => [ 2 , 3 , 6 , 7]
     *
     * @param $value
     * @return array
     */
    public function trans($value)
    {
        $result = [];

        $scope = $this->getScope();

        if (empty($scope)) {
            return $result;
        }

        $keys = array_keys($scope);

        $comb = $this->getCombinations(implode('', $keys));

        $result = [];

        if ($comb) {
            foreach ($comb as $key) {
                if (strpos($key, $value . '') !== false) {
                    $length = strlen($key);
                    $data = 0;
                    for ($i = 0; $i < $length; $i++) {
                        if (!$data) {
                            $data = (int)$key[$i];
                        } else {
                            $data = (int)$data | $key[$i];
                        }
                    }
                    $result[] = $data;
                }

            }
        };

        return $result;
    }


    /**
     * 7 转化为 xx,xx,xx
     * xx是getScope方法对应的值
     * @param $value
     * @return string
     */
    public function getText($value)
    {
        $result = [];

        $scope = $this->getScope();

        if (empty($scope)) {
            return $result;
        }

        $result = [];
        foreach ($scope as $key => $vo) {
            if ($key & $value) {
                $result[] = $vo;
            }
        }

        return implode(',', $result);
    }
}