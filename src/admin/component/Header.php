<?php

namespace magein\php_tools\admin\component;


class Header
{
    private $field = '';

    private $width = '';

    private $edit = 'text';

    private $templet = '';

    private $type = '';

    private $fixed = '';

    private $hide = '';

    private $align = '';

    private $toolbar = '';

    /**
     * @return string
     */
    public function getField(): string
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
     * @return string
     */
    public function getWidth(): string
    {
        return $this->width;
    }

    /**
     * @param string $width
     * @return $this
     */
    public function setWidth(string $width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return string
     */
    public function getEdit(): string
    {
        return $this->edit;
    }

    /**
     * @param string $edit
     * @return $this
     */
    public function setEdit(string $edit)
    {
        $this->edit = $edit;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplet(): string
    {
        return $this->templet;
    }

    /**
     * @param string $templet
     * @return $this
     */
    public function setTemplet(string $templet)
    {
        $this->templet = $templet;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
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
    public function getFixed(): string
    {
        return $this->fixed;
    }

    /**
     * @param string $fixed
     * @return $this
     */
    public function setFixed(string $fixed)
    {
        $this->fixed = $fixed;

        return $this;
    }

    /**
     * @return string
     */
    public function getHide(): string
    {
        return $this->hide;
    }

    /**
     * @param string $hide
     * @return $this
     */
    public function setHide(string $hide)
    {
        $this->hide = $hide;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlign(): string
    {
        return $this->align;
    }

    /**
     * @param string $align
     * @return $this
     */
    public function setAlign(string $align)
    {
        $this->align = $align;

        return $this;
    }

    /**
     * @return string
     */
    public function getToolbar(): string
    {
        return $this->toolbar;
    }

    /**
     * @param string $toolbar
     * @return $this
     */
    public function setToolbar(string $toolbar)
    {
        $this->toolbar = $toolbar;

        return $this;
    }


}