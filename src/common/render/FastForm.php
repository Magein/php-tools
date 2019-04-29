<?php

namespace magein\php_tools\common\render;

use magein\php_tools\think\Dictionary;

class FastForm
{
    /**
     * @var array
     */
    private $formItems = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $value = [];

    /**
     * @var array
     */
    private $title = [];

    /**
     * FastForm constructor.
     * @param array $data
     * @param null $dictionary
     */
    public function __construct($data = [], $dictionary = null)
    {
        $this->setData($data);

        $this->setTitle($dictionary);
    }

    /**
     * 组合好后用于重置选项，如继承类，子类中不需要父类中那么多选项，调用items后，unset掉后在调用此方法重置即可
     * @param $item
     */
    public function setItems($item)
    {
        $this->formItems = $item;
    }

    /**
     * @return array
     */
    public function items()
    {
        return $this->formItems;
    }

    /**
     * 设置title
     * @param $dictionary
     */
    private function setTitle($dictionary)
    {

        $title = [];

        if (is_array($dictionary)) {
            $title = $dictionary;
        } else {
            if (is_object($dictionary)) {
                $instance = $dictionary;
            } else {
                $instance = $dictionary ? new $dictionary() : null;
            }
            $word = 'word';
            if ($instance && property_exists($instance, $word)) {
                $title = $instance->$word;
            }
        }

        $this->title = array_merge(
            (new Dictionary())->word,
            $title
        );
    }

    /**
     * @param $data
     */
    private function setData($data)
    {
        $this->data = $data;

        if (isset($data['id'])) {
            $this->setHidden('id');
        }
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string|RenderData $render
     * @param string $name
     * @param string $title
     * @param bool $required
     * @param string|array $value
     * @param array $attrs
     * @return array
     */
    public function properties($render, $name = '', $title = '', $required = true, $value = '', $attrs = [])
    {
        if ($render instanceof RenderData) {
            $type = $render->getType();
            $name = $render->getName();
            $title = $render->getTitle();
            $required = $render->getRequired();
            $value = $render->getValue();
            $attrs = $render->getAttrs();
            $option = $render->getOption();
            $express = $render->getExpress();
        } else {
            $type = $render;
            $option = null;
            $express = 'eq';
        }

        if (empty($name)) {
            return [];
        }

        /**
         * 设置默认的标题
         */
        if (empty($title) && $this->title) {
            $title = isset($this->title[$name]) ? $this->title[$name] : $name;
        }

        /**
         * 设置默认的值
         */
        if (isset($this->data[$name])) {
            $value = $this->data[$name];
        }
        $this->value[$name] = $value;

        $properties = [
            'type' => $type,
            'name' => $name,
            'title' => $title,
            'required' => $required,
            'value' => $value,
            'option' => $option,
            'attrs' => $attrs,
            'express' => $express
        ];

        if (!isset($properties['placeholder'])) {
            $properties['placeholder'] = '请输入' . $title;
        }

        $this->formItems[$name] = $properties;

        return $properties;
    }

    /**
     * @param string $name
     * @param string $title
     * @param bool $required
     * @param mixed $value
     * @param array $attrs
     * @return $this
     */
    public function setText($name, $value = '', $required = true, $title = null, $attrs = [])
    {
        $this->properties('text', $name, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $name
     * @param string $value
     * @param bool $required
     * @param null $title
     * @param array $attrs
     * @return $this
     */
    public function setDateTime($name, $value = '', $required = true, $title = null, $attrs = [])
    {
        $properties['dateFormat'] = 'datetime';

        $this->properties('datetime', $name, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $name
     * @param string $value
     * @param bool $required
     * @param null $title
     * @param array $attrs
     * @return $this
     */
    public function setDate($name, $value = '', $required = true, $title = null, $attrs = [])
    {
        $properties['dateFormat'] = 'date';

        $this->properties('datetime', $name, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $name
     * @param string $value
     * @param bool $required
     * @param null $title
     * @param array $attrs
     * @return $this
     */
    public function setTime($name, $value = '', $required = true, $title = null, $attrs = [])
    {
        $properties['dateFormat'] = 'time';

        $this->properties('datetime', $name, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $name
     * @param string $dateFormat
     * @param string $value
     * @param bool $required
     * @param null $title
     * @param array $attrs
     * @return $this
     */
    public function setDateTimeRange($name, $dateFormat = '', $value = '', $required = true, $title = null, $attrs = [])
    {
        $properties['dateFormat'] = $dateFormat ? $dateFormat : 'datetime';
        $properties['range'] = '~';

        $this->properties('datetime', $name, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $name
     * @param string $value
     * @param array $attrs
     * @return $this
     */
    public function setHidden($name, $value = '', $attrs = [])
    {
        $this->properties('hidden', $name, '', false, $value, $attrs);

        return $this;
    }

    /**
     * @param $name
     * @param array $options
     * @param string $value
     * @param bool $required
     * @param string $title
     * @param array $attrs
     * @return $this
     */
    public function setRadio($name, $options = [], $value = '', $required = true, $title = '', $attrs = [])
    {
        $properties['options'] = $options;

        $this->properties('radio', $name, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $name
     * @param array $options
     * @param mixed $value
     * @param bool $required
     * @param string $title
     * @param array $attrs
     * @return $this
     */
    public function setCheckbox($name, $options = [], $value = '', $required = true, $title = '', $attrs = [])
    {
        $properties['options'] = $options;

        $this->properties('checkbox', $name, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $name
     * @param array $options
     * @param string $value
     * @param bool $required
     * @param string $title
     * @param array $attrs
     * @return $this
     */
    public function setSelect($name, $options = [], $value = '', $required = true, $title = '', $attrs = [])
    {
        $properties['options'] = $options;

        $this->properties('select', $name, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $name
     * @param array $options
     * @param string $value
     * @param bool $required
     * @param string $title
     * @param array $attrs
     * @return $this
     */
    public function setSelectChecked($name, $options = [], $value = '', $required = true, $title = '', $attrs = [])
    {
        $properties['options'] = $options;

        $this->properties('select-checked', $name, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $names
     * @return $this
     */
    public function setTexts($names)
    {
        if (!is_array($names)) {
            $names = explode(',', $names);
        }

        if ($names) {
            foreach ($names as $name) {
                $this->setText($name);
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string|null $title
     * @param bool $required
     * @param mixed $value
     * @param array $attrs
     * @return $this
     */
    public function setTextArea($name, $value = '', $required = true, $title = null, $attrs = [])
    {
        $this->properties('textArea', $name, $title, $required, $value, $attrs);

        return $this;
    }
}