<?php

namespace magein\php_tools\traits;

use magein\php_tools\common\Variable;

trait ObjectInit
{
    /**
     * @var array
     */
    private $origin = [];

    /**
     * @return array
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param $data
     * @return $this
     */
    public function init($data)
    {
        if ($data && is_array($data)) {
            $variable = new Variable();
            foreach ($data as $key => $item) {
                $method = 'set' . $variable->transToPascal($key);
                if (method_exists($this, $method)) {
                    $this->$method($item);
                } else {
                    $this->origin[$key] = $item;
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];

        try {
            $class = new \ReflectionClass($this);
            $properties = $class->getProperties();
            foreach ($properties as $item) {
                $name = $item->getName();
                $method = 'get' . ucfirst($name);
                if (method_exists($this, $method)) {
                    $result[$name] = $this->$method();
                }
            }
        } catch (\ReflectionException $exception) {

        }

        return $result;
    }
}