如果按默认方式安装好 scaffold， 会在项目根目录有一个 `Modules` 目录。

请将以下代码放入 `Modules/Blog/Starter.php`

然后执行  `php artisan zgldh:module:create Modules/Blog/Starter`

```php
<?php

namespace Modules\Blog;

use Modules\User\Models\User;
use zgldh\Scaffold\Installer\HtmlFields\DateRange;
use zgldh\Scaffold\Installer\HtmlFields\Radio;
use zgldh\Scaffold\Installer\HtmlFields\Select;
use zgldh\Scaffold\Installer\ModuleStarter;

class Starter extends ModuleStarter
{
    protected function defineTitle()
    {
        return '博客';
    }

    protected function defineModels()
    {
        $models = [
            $this->defineBlogModel(),
            $this->defineCommentModel(),
        ];

        return $models;
    }

    private function defineBlogModel()
    {
        $blog = $this->newModel('blog');
        $blog->title('博客');
        $blog->setMiddleware('auth');
        $blog->useActivityLog();

        $blog->addField('title', 'string')->label('标题')
            ->htmlType('text')->required()->unique()->inIndex();
        $blog->addField('content', 'text')->label('内容')
            ->htmlType('textarea')->inIndex()->required();
        $blog->addField('password', 'string')->label('阅读密码')
            ->htmlType('password');
        $blog->addField('email', 'string')->label('反馈邮箱')
            ->htmlType('email')->validations('email');
        $blog->addField('category', 'string(15)')->label('分类')
            ->htmlType(new Select(['news' => '新闻', 'sport' => '运动']))->nullable()->inIndex();
        $blog->addField('status', 'integer')->label('状态')
            ->defaultValue(1)->inIndex()->htmlType(new Radio([1 => '草稿', 2 => '发布']));
        $blog->addField('created_by', 'integer:unsigned')->label('创建者')
            ->nullable()->inIndex()->htmlType('select')
            ->belongsTo(User::class, 'created_by', 'id');
        $blog->softDelete();

        $blog->addSearch('created_at', with(new DateRange())->setLabel('Created At'));

        return $blog;
    }

    private function defineCommentModel()
    {
        $comment = $this->newModel('comment');
        $comment->title('评论');
        $comment->setMiddleware('auth');
        $comment->useActivityLog();

        $comment->addField('content', 'text')->label('内容')
            ->htmlType('textarea')->inIndex()->nullable();
        $comment->addField('email', 'string')->label('邮箱')
            ->htmlType('email')->validations('email');
        $comment->addField('user_id', 'integer:unsigned')->label('作者')
            ->nullable()->inIndex()->htmlType(new Select())
            ->belongsTo(User::class, 'user_id', 'id');

        $comment->addSearch('created_at', with(new DateRange())->setLabel('Created At'));

        return $comment;
    }
}

```
