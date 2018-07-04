## zgldh/Scaffold

基于 Laravel 5.5， Vue 2， ElementUI 2， vueAdmin-template 制作的后台脚手架。

###预制功能：

- 用户，角色，权限体系
- 文件上传
- 通知系统
- 完全脱离 Cookie/Session 机制。全面拥抱 JWT。自动刷新 Token
- 强化的前端数据表格，使用 datatables 协议。
- 多语言

###生成器：

|名称|命令
|----|----|
|模块初始化 | scaffold:module \<ModuleName\> |
|模型初始化 | scaffold:model \<ModelStarterClass\> |
|API 生成 | scaffold:api \<method> \<URL> \<ModuleName>|

### 开始使用

1. git clone
2. `php artisan scaffold:init`

- It will run 
  - migrate
  - storage:link
  - db:seed --class=ScaffoldInitialSeeder

`php artisan scaffold:make TableName1 TableName2 TableName3 --module=ModuleName --version=V2`

- TODO It will generate

`php artisan scaffold:api:add PUT user/{user_id}/mobile ModuleName`

- It will generate PHP route/Request/Controller and Vue API


仍然处在开发阶段，不稳定。
打算正式版的时候，全盘转入token认证。也就是laravel里面的 auth:api


### 感谢

