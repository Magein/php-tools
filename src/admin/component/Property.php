<?php

namespace magein\php_tools\admin\component;

use magein\php_tools\traits\ObjectInit;

class Property
{
    use ObjectInit;

    /**
     * 标题
     * @var string
     */
    protected $title = null;

    /**
     * 类型
     * @var string
     */
    protected $type = null;

    /**
     * 名称
     * @var string
     */
    protected $field = null;

    /**
     * 默认值
     * @var string|array
     */
    protected $value = null;

    /**
     * 是否必填
     * @var bool
     */
    protected $required = true;

    /**
     * placeholder
     * @var string
     */
    protected $placeholder = null;

    /**
     * 选项
     * @var array
     */
    protected $option = [];

    /**
     * 其他属性
     * @var array
     */
    protected $attrs = [];

    /**
     * Item constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->origin = $data;

        $this->init($data);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * @return string
     */
    public function getType()
    {
        return $this->type ?: 'text';
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function setField(string $field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param array|string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     * @return $this
     */
    public function setRequired(bool $required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     * @return $this
     */
    public function setPlaceholder(string $placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return array
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param array $option
     * @return $this
     */
    public function setOption(array $option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttrs()
    {
        return $this->attrs;
    }

    /**
     * @param array $attrs
     * @return $this
     */
    public function setAttrs(array $attrs)
    {
        $this->attrs = $attrs;

        return $this;
    }
}
