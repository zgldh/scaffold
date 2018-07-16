# zgldh/Scaffold

基于 Laravel 5.5， Vue 2， ElementUI 2， vueAdmin-template 制作的后台脚手架。

预制功能：
================

- 用户，角色，权限体系
- 文件上传
- 通知系统
- 完全脱离 Cookie/Session 机制。全面拥抱 JWT。自动刷新 Token
- 强化的前端数据表格，使用 datatables 协议。
- 多语言

开始使用
=============

1. `composer create-project zgldh/scaffold your-project-dir`
2. 配置好 `.env` 数据库相关 
3. `php artisan scaffold:init`
  
   会自动执行以下命令 
   
   - `migrate`
   - `storage:link`
   - `permission:auto-refresh`
   - `db:seed --class=ScaffoldInitialSeeder`
   - `lang:dump`
  
4. 配置好 `frontend/config/dev.env.js` 的 `BASE_API `
5. `npm install`
6. `npm run start`

生成器
==========

|名称|命令
|----|----|
|模块初始化 | `scaffold:module {moduleName} {--force}` |
|模型初始化 | `scaffold:model {modelStarterClass} {--only=*} {--force}` |
|API 生成 | `scaffold:api {method} {route} {moduleName} {--controller=} {--action=}`|
|权限生成|`permission:auto-refresh {type=api : set guard name}`
|通知生成|`notifications:create {moduleName} {notificationName}`
|语言文件导出|`lang:dump`|

模块初始化 
----------
`scaffold:module {moduleName} {--force}`

模块是指一个独立的功能领域。使用本命令将初始化一个模块。
 
 **Example**
 
 `scaffold:module Post`
  
  将创建好如下目录和文件:
   
   - `Modules/Post`
   - `Modules/Post/routes.php`
   - `Modules/Post/PostServiceProvider.php`
   - `frontend/src/store/modules/post.js`
   
  并自动修改好如下文件：
  
   - `config/api.php`
   - `routes/api.php`
   - `frontend/src/store/index.js`
 
模型初始化 
----------
`scaffold:model {modelStarterClass} {--only=*} {--force}`

模型是指数据模型，对应着一个数据表。需要一个 Starter Class 来描述该模型。

使用本命令将初始化该模型的`migration file`, `controller`, `model`,
 `request`, `repository`, `route`, `factory`, PHP 单元测试和前端脚手架文件。基本的 CRUD 都准备好了。
 
如何编写 Starter Class 请参考源码: `Modules\Post\PostStarter.php`

`--only` 取值： 
       controller,
       request,
       repository,
       model,
       migration,
       api,
       resource,
       language,
       route,
       factory,
       phpunit 将只生成对应文件。
 
 **Example**
 
 `scaffold:model Modules/Post/PostStarter.php`
  
  将创建好如下目录和文件:
   
   - `Modules/Post`
   - `Modules/Post/Controllers/PostController.php`
   - `Modules/Post/Repositories/PostRepository.php`
   - `Modules/Post/Models/Post.php`
   - `Modules/Post/Requests/CreatePostRequest.php`
   - `Modules/Post/Requests/UpdatePostRequest.php`
   - `resources/lang/en/post.php`
   - `resources/lang/zh-CN/post.php`
   - `database/migrations/xxxx_xx_xx_xxxxxx_create_posts_table.php`
   - `database/factories/PostFactory.php`
   - `tests/Modules/Post`
   - `tests/Modules/Post/Post/PostIndexTest.php`
   - `tests/Modules/Post/Post/PostStoreTest.php`
   - `tests/Modules/Post/Post/PostShowTest.php`
   - `tests/Modules/Post/Post/PostUpdateTest.php`
   - `tests/Modules/Post/Post/PostDestroyTest.php`
   - `frontend/src/api/post.js`
   - `frontend/src/views/post`
   - `frontend/src/views/post/Post/List.vue`
   - `frontend/src/views/post/Post/Editor.vue`
   
  并自动修改好如下文件：
  
   - `Modules/Post/routes.php`
   - `frontend/src/router/dynamicRouterMap.js`

API 生成 
----------
`scaffold:api {method} {route} {moduleName} {--controller=} {--action=}`

方便的生成一个单独的 API 和周边的各种类、单元测试、前台接口等。
 
 **Example**
 
 `scaffold:api put /post/{id}/like Post`
  
  将创建好如下目录和文件:
   
   - `Modules/Post/Requests/PutIdLikeRequest.php`
   - `Modules/Post/routes.php`
   - `tests/Modules/Post/Post/PutIdLikeTest.php`
   
  并自动修改好如下文件：
  
   - `Modules/Post/Controllers/PostController.php`
   - `Modules/Post/routes.php`
   - `frontend/src/api/post.js`
   
  并创建权限：
  
   - Post@putIdLike
   
权限生成 
----------
`permission:auto-refresh {type=api : set guard name}`

遍历 `Modules` 下所有的 `controller` 和 `repository`。 根据其公共函数生成一系列权限，并自动赋予超级管理员。
 
如果函数的注释内，包含有 `@no-permission` 标记，则跳过该函数。
 
 **Example**
 
 `permission:auto-refresh`
  
  将自动修改对应 `model` 的语言文件的 `permissions` 数组，并创建一系列权限。
  
  会自动跳过重复权限。

通知生成
---------
`notifications:create {moduleName} {notificationName}`

创建一个 Notification 类。

  
语言文件导出
-----------
`lang:dump`

将 PHP 语言文件导出为前端语言文件。使得前端 `vue-i18n` 组件也可使用。

导出产物储存在 `frontend/src/lang/languages.js`


组件说明
===========

TODO

预制功能说明
===========

TODO

感谢
===========

https://github.com/laravel/laravel

http://element-cn.eleme.io/

https://github.com/PanJiaChen/vueAdmin-template

http://webpack.github.io/
