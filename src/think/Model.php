<?php

namespace magein\php_tools\think;

use traits\model\SoftDelete;

class Model extends \think\Model
{
    use SoftDelete;

    protected $pk = 'id';

    protected $autoWriteTimestamp = true;

    protected $deleteTime = 'delete_time';

    protected $dateFormat = 'Y-m-d H:i:s';
}