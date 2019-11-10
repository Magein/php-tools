### 描述

  个人开发常用的工具类，src/think下的文件需要依赖tp5
  
  thinkphp 5.0.24最好
  
  目前发现的问题：5.0.10中的Request类中的action方法跟5.0.24有所区别
  
### common 

 常用的一些工具类，原则上不依赖任何框架以及不存在继承关系，
 
### extra 

 需要依赖第三方的插件支持
 
 
### think

 需要依赖thinkphp5的框架支持

### 维护日志

 2019-05-05:
 * 修改updateField方法为public类型
 * 新增$order属性信息，用于调整列表属性
 * 修改设置返回的数据键设置出错的bug
 * 修改分页参数类型为数组
 * getClass类增加一个参数，用于设置命名空间前缀
 
 2019-05-06：
 * 增加extra目录，用于二次封装第三方插件
 * 修改表单name属性获取的值为field（跟表格中的保持一致）
 
 2019-05-07
 * 增加表单的grid属性，用于控制表单项的大小
 
 2019-05-11
 * 删除admin文件夹
 
 2019-05-14
 * think目录下增加ApiReturn.php 用于相应接口请求
 * think目录下增加ApiService.php 用于为接口提供服务(处理参数，返回数据)
 * think目录下增加ApiException.php 用于为接口处理http请求错误信息（返回json数据）
 
 2019-05-20
 * common下增加TreeStructure类，用于处理树结构数据
 * ApiException类优化返回的错误信息
 
 2019-05-22
 * 优化ueditor和upload类，上传图片后返回的路径信息默认使用包含host模式
 
 2019-05-30
 * 优化logic中一对多模型，变量错误，导致查询的结果中没有相关数据
 
 2019-05-31
 * 优化logic中的setPage方法，由private修改为protected
 * 优化logic中的select逻辑语法
 
 2019-06-03
 * 使用自定的UnixTime替代内置的 strtotime 用法
 * 优化extra下的Model模型，增加了startTime和endTime字段的获取器和修改器
 * 优化extra下的Logic类下的updateField方法，在更新前，执行了查询的操作，确保数据存在，且使用了对象的方式执行，确保在后续使用修改器的时候可以通过第二个参数拿到查询出来的数据
 
 2019-06-04
 * 优化upload上传的方法类,
 
 2019-06-14
 * 优化logic类的save方法，对传递的参数进行去重操作（仅字符串类型）
 
 2019-06-19
 * 优化了一个逻辑，在setError的方法中返回false，这样在子类中可以直接使用return $this->setError()了
 * 优化TreeStructure中的title没有办法设置的问题
 
 2019-08-06
 * Logic中的条件查询会对上下文查询照成污染，重新优化了该逻辑
 
 2019-11-05
  * Logic中保存主动调用清除缓存的方法
  
 2019-11-06
    * Logic中新增getList方法用于获取数据，如果携带条件则从数据库查询
    
 2019-11-10
    * Curl类新增$url变量和getUrl方法、get请求url使用urldecode进行解码
    * BaiDuMap类request请求由private修改为public
    * UnixTime类新增获取月的起始时间和结束时间的方法