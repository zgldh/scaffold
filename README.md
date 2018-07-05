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

1. composer create-project zgldh/scaffold
2. 配置好 .env
3. `php artisan scaffold:init`
  
   会自动执行以下命令 
   
   - migrate
   - storage:link
   - db:seed --class=ScaffoldInitialSeeder
  
4. npm install
5. npm run start

### 感谢

