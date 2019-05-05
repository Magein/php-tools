<?php

namespace magein\php_tools\think;

use magein\php_tools\common\Variable;
use magein\php_tools\traits\Error;
use think\Model;
use think\Paginator;
use think\paginator\driver\Bootstrap;
use think\Validate;

/**
 * 这里使用了抽象类,在子类中必须实现 model 方法
 *
 * logic要区别于model
 *
 * logic是包含业务逻辑在里面的,model就是一个模型,里面的方法都是针对模型的,tp框架提供的方法写在model中
 *
 * 实际的业务逻辑写在logic中,虽然logic定义的有些写法跟model类相似,如定义查询的字段,使用logic->setFields()
 *
 * Class MainLogic
 * @package app\common\logic
 */
abstract class Logic
{
    use Error;

    /**
     * @return Model
     */
    abstract protected function model();

    const ERROR_PARAMS_NOT_NULL = '参数不能为空，请确认';

    const ERROR_SERVICE_ERROR = '服务器内部错误，请稍后再试';

    const ERROR_OPERATION_FAIL = '操作失败，请稍后再试';

    /**
     * 主键
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * tp中的一对多模型
     * @var array
     */
    protected $hasMany = [];

    /**
     * tp中的一对一模型
     * @var array
     */
    protected $hasOne = [];

    /**
     * 关联预载入 https://www.kancloud.cn/manual/thinkphp5/139045
     * @var array
     */
    protected $with = [];

    /**
     * tp中的设置模型中不存在的值，需要追加进去
     * @var array
     */
    protected $appendAttr = [];

    /**
     * 这里是默认查询的字段信息, 针对不不同的业务,应该把查询的字段整齐划一的规划起来
     *
     * 如:一个商品的基础信息和详细信息(规格,描述,介绍等text类型的字段信息,这里主要是为了优化查询速度)
     *
     * protected $baseField=[id,title,sort,cate_name,];
     *
     * protected $infoField=[id,title,sort,intro,spec];
     *
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $condition = [];

    /**
     * @var string
     */
    protected $order = '';

    /**
     * @var string
     */
    protected $group = '';

    /**
     * @var Validate
     */
    protected $validate = null;

    /**
     * 默认不查询已经软删除的数据
     * @var bool
     */
    protected $withTrashed = false;

    /**
     * 是否处理成数组
     * @var bool
     */
    protected $transArray = true;

    /**
     * @var string
     */
    protected $returnArrayKey = '';

    /**
     * @var array
     */
    protected $allowTime = [
        'today' => '今天',
        'yesterday' => '昨天',
        'week' => '本周',
        'last week' => '上周',
        'month' => '本月',
        'last month' => '上个月',
        'year' => '今年',
        'last year' => '去年',
    ];

    /**
     * @var array
     */
    protected $pageParams = [];

    /**
     * @var string
     */
    protected $pageRender = '';

    /**
     * @var null
     */
    protected static $instance = [];

    /**
     * 在同一个生命周期内的类实例唯一
     * @return static
     */
    public static function instance()
    {
        if (!isset(self::$instance[static::class])) {
            self::$instance[static::class] = new static();
        }

        return self::$instance[static::class];
    }

    /**
     * @param string $primaryKey
     * @return $this
     */
    protected function setPk($primaryKey = '')
    {
        if (empty($primaryKey)) {
            $this->primaryKey = $this->model()->getPk();
        } else {
            $this->primaryKey = $primaryKey;
        }

        return $this;
    }

    /**
     * 字段信息转化为驼峰
     * @param $fields
     * @return array
     */
    private function fieldToUnderline($fields)
    {
        if (empty($fields)) {
            return [];
        }

        if (!is_array($fields)) {
            $fields = [$fields];
        }

        $variable = new Variable();

        foreach ($fields as $key => $field) {
            $fields[$key] = $variable->transToUnderline($field);
        }

        return $fields;
    }

    /**
     * @param array|string $fields
     * @return $this
     */
    public function setHasMany($fields)
    {
        $this->hasMany = $this->fieldToUnderline($fields);

        return $this;
    }

    /**
     * @param array|string $fields
     * @return $this
     */
    public function setHasOne($fields)
    {
        $this->hasOne = $this->fieldToUnderline($fields);

        return $this;
    }

    /**
     * @param array|string $fields
     * @return $this
     */
    public function setWith($fields)
    {
        if (is_array($fields)) {
            $fields = implode(',', $fields);
        }

        $this->with = $fields;

        return $this;
    }

    /**
     * @param array|string $fields
     * @return $this
     */
    public function setAppendAttr($fields)
    {
        $this->appendAttr = $this->fieldToUnderline($fields);

        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param string $order
     * @return $this
     */
    public function setOrder($order = 'id desc')
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @param array $condition
     * @return $this
     */
    public function setCondition(array $condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @param $group
     * @return $this
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @param Validate|string $validate
     * @return $this
     */
    public function setValidate(Validate $validate)
    {
        $this->validate = $validate;

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setReturnArrayKey($key)
    {
        $this->returnArrayKey = $key ? $key : 'id';

        return $this;
    }

    /**
     * 查询结果自动处理成数组
     * @param bool $bool
     * @return $this
     */
    public function setTransArray($bool = true)
    {
        $this->transArray = $bool === true ? true : false;

        return $this;
    }

    /**
     * 设置是否查询已经删除的数据
     * @param bool $withTrashed
     * @return $this
     */
    public function setWithTrashed($withTrashed = false)
    {
        $this->withTrashed = $withTrashed ? true : false;

        return $this;
    }

    /**
     * 获取验证类
     * @return null|Validate
     */
    public function getValidate()
    {
        if ($this->validate) {
            $validate = $this->validate;
            if ($validate instanceof Validate) {
                return $validate;
            } elseif (class_exists($validate)) {
                return new $validate();
            }
        }

        return null;
    }

    /**
     * 获取分页参数，返回的是一个数组
     * @return array
     */
    public function getPageParams()
    {
        return $this->pageParams;
    }

    /**
     * 返回tp自带的分页代码
     * @return string
     */
    public function getPageRender()
    {
        return $this->pageRender;
    }

    /**
     * @return array
     */
    public function getAllowTime()
    {
        return $this->allowTime;
    }

    /**
     * 根据主键获取信息
     * @param $pk
     * @return array|bool
     */
    public function get($pk)
    {
        if (empty($pk)) {
            return false;
        }

        $model = $this->db();

        $model->where($model->getPk(), $pk);

        $record = call_user_func_array([$model, 'find'], []);

        return $this->toArray($record);
    }

    /**
     * 获取一条数据记录
     * @return array|bool|null
     */
    public function find()
    {
        if (empty($this->condition)) {
            return false;
        }

        $model = $this->db();

        $record = call_user_func_array([$model, 'find'], []);

        return $this->toArray($record);
    }

    /**
     * 查询数据列表，不包含分页
     * @return array|mixed
     */
    public function select()
    {
        $db = $this->db();

        $records = call_user_func_array([$db, 'select'], []);

        /**
         * 预处理查询，预处理需要先查询出数据后，在使用关键的字段进行查询
         */
        if ($this->with) {
            $pk = [];
            foreach ($records as $key => $record) {
                if (isset($record[$this->primaryKey])) {
                    $pk[] = $record[$this->primaryKey];
                }
            }
            if ($pk) {
                $db = $this->db();
                $db->with($this->with);
                $records = call_user_func_array([$db, 'select'], [$pk]);
            }
        }

        return $this->toArray($records);
    }

    /**
     * 设置分页参数
     * @param $records
     * @param int $limit
     * @return array
     */
    private function setPage($records, $limit = 15)
    {
        $pages = [
            // 总数
            'total' => 1,
            // 每页数量
            'per_page' => $limit,
            // 当前页
            'current_page' => 1,
            // 最后一页
            'last_page' => 1,
            // 是否还要更多
            'has_more' => 0,
        ];


        if (empty($records) || !$records instanceof Bootstrap) {
            return $pages;
        }

        /**
         * tp默认的分页代码
         */
        $this->pageRender = $records->render();

        /**
         * 分页参数
         */
        $pages['total'] = $records->total();
        $pages['current_page'] = $records->currentPage();
        $pages['last_page'] = $records->lastPage();
        $pages['has_more'] = $records->hasPages();

        $this->pageParams = $pages;

        return $pages;
    }

    /**
     * @param int $limit
     * @return array
     */
    public function paginate($limit = 15)
    {
        $db = $this->db();

        /**
         * @var Paginator $records
         */
        $records = call_user_func_array([$db, 'paginate'], [$limit]);

        /**
         * 分页信息
         */
        $this->setPage($records, $limit);

        $items = $records->items();

        if ($items) {
            if ($this->with) {
                $pk = [];
                foreach ($items as $key => $record) {
                    if (isset($record[$this->primaryKey])) {
                        $pk[] = $record[$this->primaryKey];
                    }
                }
                if ($pk) {
                    /**
                     * 这里需要重新实例化,不然设置的field会失效
                     *
                     * 关联预载入是解决多次查询关联数据的问题
                     */
                    $db = $this->db();
                    $db->with($this->with);
                    $items = call_user_func_array([$db, 'select'], [$pk]);
                }
            }
        }

        return $this->toArray($items);
    }

    /**
     * @return Model
     */
    protected function db()
    {
        $db = $this->model();

        /**
         * 如果需要查询软删除的数据，则重新获取db实例
         */
        if ($this->withTrashed && method_exists($db, 'withTrashed')) {
            $db = call_user_func_array([$db, 'withTrashed'], []);
        }

        /**
         * 设置主键的值
         */
        $this->setPk($db->getPk());

        if ($this->with) {
            $db->with($this->with);
        }

        if ($this->condition) {
            $db->where($this->condition);
        }

        if ($this->fields) {
            $db->field($this->fields);
        }

        if ($this->order) {
            $db->order($this->order);
        }

        if ($this->group) {
            $db->group($this->group);
        }

        return $db;
    }

    /**
     *
     * 这里是对模型类转化为数组的方法,其功能包含
     * 1. 数据转化为数组
     * 2. 如果子类中定义了要追加的元素信息,则以字段的形式添加到数组中
     * 3. 如果子类中定义了一对一模型的名称,则以数组的形式添加到数组中
     * 4. 如果子类中定义了一对多模型的名称,则以多为数组的形式添加到数组中
     *
     *
     * 这里需要注意的是
     * 追加的元素是在Model类中使用 getXXXAttr()定义的
     * 一对一模型,一对多模型是在Model类中使用方法实现的,方法使用驼峰命名法
     * 方法名称作为键的方式把对应的数据传给外部使用,默认使用下划线的方式
     *
     * 如方法名是: goodInfo(),  那最终在字段里面显示是  good_info:{id:xx,title:xx}
     * @param Model $record
     * @return array|Model
     */
    protected function toArray($record)
    {
        if (false === $this->transArray) {
            return $record;
        }

        if (empty($record)) {
            return null;
        }

        $variable = new Variable();

        /**
         * @param Model $model
         * @return array
         */
        $toArray = function (Model $model) use ($variable) {

            if ($this->appendAttr) {
                $model->append($this->appendAttr);
            }

            /**
             * 处理一对模型
             */
            if ($this->hasOne) {
                foreach ($this->hasOne as $many) {
                    $many_data = $model[$many];
                    unset($model[$many]);
                    $many = $variable->transToUnderline($many);
                    $model[$many] = $many_data ? $many_data : null;
                }
            }

            /**
             * 处理一对多模型
             */
            if ($this->hasMany) {
                foreach ($this->hasMany as $many) {
                    $many_data = $model[$many];
                    // 这里默认只处理两级
                    if ($many_data && is_array($many_data)) {
                        foreach ($many_data as $key => $datum) {
                            $many_data[$key] = $datum;
                        }
                    }
                    // 转化成下划线格式
                    unset($model[$many]);
                    $many = $variable->transToUnderline($many);
                    $record[$many] = $many_data ? $many_data : null;
                }
            }

            return $model->toArray();
        };

        // 清除条件语句，防止连续调用产生的条件污染
        $this->setCondition([]);

        if ($record instanceof Model) {
            return $toArray($record);
        }

        $data = [];
        if (is_array($record)) {
            foreach ($record as $item) {
                if ($item instanceof Model) {
                    $result = $toArray($item);
                    if ($this->returnArrayKey && $result[$this->returnArrayKey]) {
                        $data[$result[$this->returnArrayKey]] = $result;
                    } else {
                        $data[] = $result;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param $pk
     * @return bool|int
     */
    public function delete($pk)
    {
        $model = $this->model();

        if (!is_array($pk)) {
            $pk = explode(',', $pk);
        }

        if (empty($pk)) {
            $this->setError(self::ERROR_PARAMS_NOT_NULL);
            return false;
        }

        $result = $model->save(
            [
                'delete_time' => time()
            ],
            [
                $model->getPk() => ['in', $pk]
            ]
        );

        return $result;
    }

    /**
     * 新增/编辑
     * 新增则返回主键的值
     * 更新也返回主键的值
     * @param array $data
     * @return bool|false|int
     */
    public function save($data = [])
    {
        if (empty($data)) {
            $this->setError(self::ERROR_PARAMS_NOT_NULL);
            return false;
        }

        $model = $this->model();

        /**
         * 数据中如果包含主键的值并且主键的值不为空,则视为编辑
         */
        if (isset($data['id'])) {
            $model_pk = 'id';
        } else {
            $model_pk = $model->getPk();

        }

        $ins_id = isset($data[$model_pk]) ? $data[$model_pk] : '';
        unset($data[$model_pk]);

        $validate = $this->getValidate();

        if ($validate) {
            // 更新的情况下，验证是否有更新的场景
            if ($ins_id && $validate->hasScene('update')) {
                $validate->scene('update');
            }
            if (!$validate->check($data)) {
                $this->setError($validate->getError() . '');
                return false;
            }
        }

        // 更新
        if ($ins_id) {
            $result = $model->allowField(true)->save($data, [$model_pk => $ins_id]);
        } else {
            // 新增
            $result = $model->allowField(true)->save($data);
            $ins_id = $model->$model_pk;
        }

        if (false === $result) {
            $this->setError(self::ERROR_OPERATION_FAIL);
            return false;
        }

        return $ins_id;
    }

    /**
     * 更新单个字段的值,可以通过设置condition的值更新,也可以通过主键的值更新
     * @param string $field
     * @param null $value
     * @param null $pkValue
     * @return bool|false|int
     */
    public function updateField($field, $value = null, $pkValue = null)
    {
        /**
         * 没有指定主键，并且没有设置条件的时候，更新是一个危险的操作，需要验证
         */
        if (empty($pkValue) && empty($this->condition)) {
            return false;
        }

        /**
         * 如果指定了主键，则清空设置的条件信息，防止设置的条件信息污染更新语句
         */
        if ($pkValue) {
            $this->condition = [];
        }

        /**
         * 主键名称 默认是id
         */
        if (empty($this->primaryKey) || empty($field) || $value === null) {
            $this->setError(self::ERROR_PARAMS_NOT_NULL);
            return false;
        }

        $validate = $this->getValidate();
        if ($validate) {
            if ($validate && $validate->hasScene($field)) {
                if (!$validate->scene($field)->check([$field => $value])) {
                    $this->setError($validate);
                    return false;
                }
            }
        }

        $model = $this->model();

        if ($this->condition) {
            $condition = $this->condition;
        } else {
            $condition[$this->primaryKey] = $pkValue;
        }

        $result = $model->save([$field => $value], $condition);

        if (false === $result) {
            return false;
        }

        $this->setError(self::ERROR_OPERATION_FAIL);

        return $result;
    }

    /**
     * 获取字段的集合
     * @param string $fields
     * @param string $key
     * @return array
     */
    public function column($fields = '', $key = '')
    {
        $model = $this->db();

        $records = $model->column($fields, $key);

        $this->setCondition([]);

        return $records;
    }

    /**
     * 获取字段的单个值
     * @param string $field
     * @param mixed $default 默认值
     * @return mixed
     */
    public function value($field = '', $default = null)
    {
        if (empty($field)) {
            return $default;
        }

        $model = $this->model();

        $records = $model->where($this->condition)->value($field, $default);

        $this->setCondition([]);

        return $records;
    }

    /**
     * @param $data
     * @return array|false
     * @throws \Exception
     */
    public function saveAll($data)
    {
        return $this->model()->saveAll($data);
    }

    /**
     * @param string $field
     * @param int $step
     * @return bool|int|true
     * @throws \think\Exception
     */
    public function setDec($field, $step)
    {
        if (empty($this->condition)) {
            return false;
        }

        $result = $this->model()->where($this->condition)->setDec($field, $step);

        $this->setCondition([]);

        return $result;
    }

    /**
     * @param $field
     * @param $step
     * @return bool|int|true
     * @throws \think\Exception
     */
    public function setInc($field, $step)
    {
        if (empty($this->condition)) {
            return false;
        }

        $result = $this->model()->where($this->condition)->setInc($field, $step);

        $this->setCondition([]);

        return $result;
    }

    /**
     * @param string $time
     * @param string $timeField
     * @return bool|mixed
     */
    protected function whereTime($time = 'today', $timeField = 'create_time')
    {
        if (!isset($this->allowTime[$time])) {
            return false;
        }

        $model = $this->db();

        $model->whereTime($timeField, $time);

        $records = call_user_func_array([$model, 'select'], []);

        if ($records) {
            foreach ($records as $key => $record) {
                $records[$key] = $this->toArray($record);
            }
        } else {
            $records = [];
        }

        return $records;
    }

}