<?php

namespace magein\php_tools\admin\component;

use magein\php_tools\traits\ObjectInit;

class Item
{
    use ObjectInit;

    /**
     * 标题
     * @var string
     */
    private $title = '';

    /**
     * 类型
     * @var string
     */
    private $type = '';

    /**
     * 名称
     * @var string
     */
    private $name = '';

    /**
     * 默认值
     * @var string|array
     */
    private $value = '';

    /**
     * 是否必填
     * @var bool
     */
    private $required = true;

    /**
     * placeholder
     * @var string
     */
    private $placeholder = '';

    /**
     * 选项
     * @var array
     */
    private $option = [];

    /**
     * 其他属性
     * @var array
     */
    private $attrs = [];

    /**
     * @var string
     */
    private $express = '';

    public function __construct($data = [])
    {
        $this->init($data);
    }

    /**
     * @return string
     */
    public function getTitle(): string
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
    public function getType(): string
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

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
    public function getRequired(): bool
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
    public function getPlaceholder(): string
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
    public function getOption(): array
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
    public function getAttrs(): array
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

    /**
     * @return string
     */
    public function getExpress(): string
    {
        return $this->express ?: 'eq';
    }

    /**
     * @param string $express
     * @return $this
     */
    public function setExpress(string $express)
    {
        $this->express = $express;

        return $this;
    }
}
