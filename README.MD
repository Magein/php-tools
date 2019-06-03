### 描述

  个人开发常用的工具类，src/think下的文件需要依赖tp5
  
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