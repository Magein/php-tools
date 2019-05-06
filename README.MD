### 描述

  个人开发常用的工具类，src/think下的文件需要依赖tp5
  
### admin

适用于后台渲染表格以及表单

需要搭配 https://gitee.com/magein/php-tools.git使用

安装admin模块 

> git clone https://gitee.com/magein/php-tools.git application/admin  

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