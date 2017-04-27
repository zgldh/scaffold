# scaffold
Scaffold for laravel


# 编码规范

1. PHP，JavaScript 类名和定义该类的文件名要一模一样，都要用首字母*大*写驼峰式。
1. PHP，JavaScript 函数名都要用首字母小写驼峰式。
2. PHP，JavaScript 变量名都要用首字母小写驼峰式。
3. PHP，JavaScript 常量名要用下划线分割的全大写字母形式。
4. PHP Model 类的命名要用单数。
4. PHP 静态函数名要用下划线分割的全大写字母形式。
5. PHP 控制器内函数只负责收集参数、浏览器输出，具体业务交给对应的 Repository 对象执行。
6. 任何变量、函数、类命名，不得使用汉语拼音。
7. composer.json 和 composer.lock 文件需要记录在版本库中。
8. 路由要求使用小写字母，单词之间使用横线链接。如 `/video-category/{id}/edit`
9. 获取、查询数据使用 GET 方式， 提交新增数据使用 POST， 改变已有数据使用 PUT， 删除使用 DELETE