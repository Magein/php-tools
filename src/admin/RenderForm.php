<?php

namespace magein\php_tools\admin;

use magein\php_tools\admin\component\Item;
use magein\php_tools\think\Dictionary;

class RenderForm
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
     * @param string|Item $render
     * @param string $field
     * @param string $title
     * @param bool $required
     * @param string|array $value
     * @param array $attrs
     * @return array
     */
    public function properties($render, $field = '', $title = '', $required = true, $value = '', $attrs = [])
    {
        if ($render instanceof Item) {
            $type = $render->getType();
            $field = $render->getField();
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

        if (empty($field)) {
            return [];
        }

        /**
         * 设置默认的标题
         */
        if (empty($title) && $this->title) {
            $title = isset($this->title[$field]) ? $this->title[$field] : $field;
        }

        /**
         * 设置默认的值
         */
        if (isset($this->data[$field])) {
            $value = $this->data[$field];
        }
        $this->value[$field] = $value;

        $properties = [
            'type' => $type,
            'name' => $field,
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

        $this->formItems[$field] = $properties;

        return $properties;
    }

    /**
     * @param string $field
     * @param string $title
     * @param bool $required
     * @param mixed $value
     * @param array $attrs
     * @return $this
     */
    public function setText($field, $value = '', $required = true, $title = null, $attrs = [])
    {
        $this->properties('text', $field, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $field
     * @param string $value
     * @param bool $required
     * @param null $title
     * @param array $attrs
     * @return $this
     */
    public function setDateTime($field, $value = '', $required = true, $title = null, $attrs = [])
    {
        $properties['dateFormat'] = 'datetime';

        $this->properties('datetime', $field, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $field
     * @param string $value
     * @param bool $required
     * @param null $title
     * @param array $attrs
     * @return $this
     */
    public function setDate($field, $value = '', $required = true, $title = null, $attrs = [])
    {
        $properties['dateFormat'] = 'date';

        $this->properties('datetime', $field, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $field
     * @param string $value
     * @param bool $required
     * @param null $title
     * @param array $attrs
     * @return $this
     */
    public function setTime($field, $value = '', $required = true, $title = null, $attrs = [])
    {
        $properties['dateFormat'] = 'time';

        $this->properties('datetime', $field, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $field
     * @param string $dateFormat
     * @param string $value
     * @param bool $required
     * @param null $title
     * @param array $attrs
     * @return $this
     */
    public function setDateTimeRange($field, $dateFormat = '', $value = '', $required = true, $title = null, $attrs = [])
    {
        $properties['dateFormat'] = $dateFormat ? $dateFormat : 'datetime';
        $properties['range'] = '~';

        $this->properties('datetime', $field, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $field
     * @param string $value
     * @param array $attrs
     * @return $this
     */
    public function setHidden($field, $value = '', $attrs = [])
    {
        $this->properties('hidden', $field, '', false, $value, $attrs);

        return $this;
    }

    /**
     * @param $field
     * @param array $options
     * @param string $value
     * @param bool $required
     * @param string $title
     * @param array $attrs
     * @return $this
     */
    public function setRadio($field, $options = [], $value = '', $required = true, $title = '', $attrs = [])
    {
        $properties['options'] = $options;

        $this->properties('radio', $field, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $field
     * @param array $options
     * @param mixed $value
     * @param bool $required
     * @param string $title
     * @param array $attrs
     * @return $this
     */
    public function setCheckbox($field, $options = [], $value = '', $required = true, $title = '', $attrs = [])
    {
        $properties['options'] = $options;

        $this->properties('checkbox', $field, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $field
     * @param array $options
     * @param string $value
     * @param bool $required
     * @param string $title
     * @param array $attrs
     * @return $this
     */
    public function setSelect($field, $options = [], $value = '', $required = true, $title = '', $attrs = [])
    {
        $properties['options'] = $options;

        $this->properties('select', $field, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $field
     * @param array $options
     * @param string $value
     * @param bool $required
     * @param string $title
     * @param array $attrs
     * @return $this
     */
    public function setSelectChecked($field, $options = [], $value = '', $required = true, $title = '', $attrs = [])
    {
        $properties['options'] = $options;

        $this->properties('select-checked', $field, $title, $required, $value, $attrs);

        return $this;
    }

    /**
     * @param $fields
     * @return $this
     */
    public function setTexts($fields)
    {
        if (!is_array($fields)) {
            $fields = explode(',', $fields);
        }

        if ($fields) {
            foreach ($fields as $field) {
                $this->setText($field);
            }
        }

        return $this;
    }

    /**
     * @param string $field
     * @param string|null $title
     * @param bool $required
     * @param mixed $value
     * @param array $attrs
     * @return $this
     */
    public function setTextArea($field, $value = '', $required = true, $title = null, $attrs = [])
    {
        $this->properties('textArea', $field, $title, $required, $value, $attrs);

        return $this;
    }
}