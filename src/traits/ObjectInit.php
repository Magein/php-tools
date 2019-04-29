<?php

namespace magein\php_tools\traits;

use magein\php_tools\common\Variable;

trait ObjectInit
{
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
                }
            }
        }

        return $this;
    }
}