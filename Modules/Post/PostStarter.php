<?php namespace Modules\Post;

use App\Scaffold\Installer\ModelStarter;
use Modules\User\Models\User;

class PostStarter extends ModelStarter
{

    /**
     * ModelNameLikeThis
     * @return string
     */
    protected function modelName()
    {
        return 'Post';
    }

    /**
     * table_name_like_this
     * @return string
     */
    protected function tableName()
    {
        return 'posts';
    }

    /**
     * [
     *      new Field()->label()->index()->required()->belongsTo().....,
     *      new Field()->label()->nullable().....
     * ]
     * @return array
     */
    protected function fields()
    {
        return [
            $this->newField('title', 'string')->label('标题')
                ->required()->unique()->factory($this->faker->unique()),
            $this->newField('content', 'text')->label('内容')
                ->inList()->required()->factory($this->faker->realText()),
            $this->newField('password', 'string')->label('阅读密码')
                ->factory($this->faker->password),
            $this->newField('email', 'string')->label('反馈邮箱')
                ->validations('email')->factory($this->faker->unique()->safeEmail),
            $this->newField('category', 'string(15)')->label('分类')
                ->nullable()->inList()->factory($this->faker->randomElement(['好人', '坏蛋'])),
            $this->newField('status', 'integer')->label('状态')
                ->defaultValue(1)->inList(),
            $this->newField('created_by', 'integer:unsigned')->label('创建者')
                ->nullable()->inList()->factory($this->faker->randomDigitNotNull)
                ->belongsTo(User::class, 'created_by', 'id'),
            $this->newField('comments')->label('评论')
                ->hasMany('Modules\Blog\Models\Comment', 'blog_id', 'id')
        ];
    }

    /**
     * @return boolean
     */
    protected function needActivityLog()
    {
        return true;
    }

    /**
     * @return boolean
     */
    protected function isSoftDelete()
    {
        return true;
    }
}
