# zgldh/Scaffold

基于 Laravel 5.5， Vue 2， ElementUI 2， vueAdmin-template 制作的后台脚手架。

升级指南: https://github.com/zgldh/scaffold/blob/master/UPDATE.md

预制功能：
================

- 用户，角色，权限体系
- 文件上传
- 通知系统
- 操作记录
- 系统设置
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

初始帐号密码： admin@email.com 123456

注意：某些虚拟机中 php artisan storage:link 命令可能会失效，请在宿主主机中执行该命令。

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
   
  并创建权限：
  
   - Post@index
   - Post@store
   - Post@show
   - Post@update
   - Post@destroy

API 生成 
----------
`scaffold:api {method} {route} {moduleName} {--controller=} {--action=}`

方便的生成一个单独的 API 和周边的各种类、单元测试、前台接口等。
 
 **Example**
 
 `scaffold:api put /post/{id}/like Post`
  
  将创建好如下目录和文件:
   
   - `Modules/Post/Requests/PutIdLikeRequest.php`
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

创建一个 Notification 类，和 markdown 邮件模板。

 **Example**

 `notifications:create post newPost`

  将创建好如下目录和文件:

   - `Modules/Post/Notifications/NewPost.php`
   - `Modules/Post/resources/views/newPost.blade.php`

  并自动修改好语言文件，请记着调整后手动执行 lang:dump：

   - `resources/lang/*/notification.php`

语言文件导出
-----------
`lang:dump`

将 PHP 语言文件导出为前端语言文件。使得前端 `vue-i18n` 组件也可使用。

导出产物储存在 `frontend/src/lang/languages.js`

组件说明
====

内置了一些常用组件。

数据表格 zgldh-datatables
---------------------
改造自 `ElementUI` 的 `table` 组件。

参数：

参数名|类型|必填|默认值|说明
------|---|----|----|---
source|[Array, Function]|true|null|数据源。通常定义为一个函数。后台请实现 [datatables 协议](https://www.datatables.net/manual/server-side)
title|String|false|null|用于数据导出的文件标题
autoLoad|Boolean|false|true|是否初始化完毕就立即执行载入
columnSelection|Boolean|false|false| 暂时没用。 Show the column selection button
enableSelection|Boolean|false|true| 允许选择行
enableAddressBar|Boolean|false|true| 允许在地址栏储存请求条件
actions|Array|false| [] | 行动作按钮。 <pre> [{Title: () => this.$i18n.t('global.terms.download'),<br>    Handle: this.handleDownload  },<br>  {    Title: () => this.$i18n.t('global.terms.edit'),<br>    Handle: this.handleEdit  },<br>  {    Title: () => this.$i18n.t('global.terms.delete'),<br>    Handle: this.handleDelete  },<br>  150  // Optional, the actions column width in px, or '10em' in custom width.  ]</pre>
multipleActions|Array|false|[]| 表格顶部动作按钮。
filters|Array|false|[]| 高级过滤器的配置。
exportColumns|Object|false|null|导出文件的列配置。<pre>{<br>  "name": this.$t('upload.fields.name'),<br>  "description": this.$t('upload.fields.description'),<br>  "disk": this.$t('upload.fields.disk'),<br>  "path": this.$t('upload.fields.path'),<br>  "size": this.$t('upload.fields.size'),<br>  "type": this.$t('upload.fields.type'),<br>  "created_at": this.$t('global.fields.created_at'),<br>}</pre>

图标组件 auto-icon
--------------
用法|描述
---|---
`<auto-icon icon-class="eye" />`|`frontend\src\icons` 里的图标
`<auto-icon icon-class="fa-bell" />`|https://fontawesome.com/icons 的图标
`<auto-icon icon-class="el-icon-bell" />`|http://element-cn.eleme.io/#/zh-CN/component/icon 的图标
`<auto-icon icon-class="ion-md-notifications" />`|https://ionicons.com 的图标

TODO

预制功能说明
===========

1. 添加新的系统设置

    比如我们要设置一个 `theme` 项，默认值是 `sunset`。
    1. 在 `Modules\Setting\Bundles\System` 的 `defaults`函数内设置该项：

    ```
    public function defaults()
    {
        return [
            'site_name'         => '管理平台',
            'site_introduction' => '<b>各种介绍</b>',
            'default_language'  => 'zh-CN',
            'target_planets'    => [
                'earth',
                'mars'
            ],
            'theme'             => 'sunset' // 这是新增的设置项
        ];
    }
    ```

    2. 修改 `frontend\src\views\Setting\index.vue` 增加输入字段
    ```
        <form-item :label="name('system','theme')">
          <el-select
            v-model="settings.theme"
            value-key=""
            reserve-keyword>
            <el-option label="星空" value="star"/>
            <el-option label="夕阳" value="sunset"/>
          </el-select>
        </form-item>
    ```

    3. （可选）新增该配置项的语言配置 `resources\lang\*\setting.php`。然后 `lang:dump` 如：

    ```
    'bundles' => [
        'system' => [
            ...
            'theme'         => '主题',
            ...
        ]
    ]
    ```

2. 使系统设置生效

    初始化好以后，系统设置只会保存设置值，但目前版本不会有任何实际作用。请手工修改类 `Modules\Setting\Bundles\System`

    注意观察里面的 `setSiteName` 和 `setDefaultLanguage` 函数，他们是当设置项的值真正改变前的钩子函数。

    你可以在这里做任何额外操作，然后将最终的设置项的值返回即可。



常用操作
====

1. 添加一个前端页面
    1. 在 `frontend/views` 下创建该页面。 建议储存到合理的子文件夹下。
    2. 在 `frontend\src\router\dynamicRouterMap.js` 里添加路由。
    3. 注意路由的 meta.title 可以设置成函数来实现多语言。
2. ...

感谢
===========

https://github.com/laravel/laravel

http://element-cn.eleme.io/

https://github.com/PanJiaChen/vueAdmin-template

http://webpack.github.io/
