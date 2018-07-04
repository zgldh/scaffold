<?php namespace Modules\Notification;

use App\Scaffold\Installer\ModelStarter;
use Modules\User\Models\User;

class NotificationStarter extends ModelStarter
{

    /**
     * ModelNameLikeThis
     * @return string
     */
    protected function modelName()
    {
        return 'Notification';
    }

    /**
     * table_name_like_this
     * @return string
     */
    protected function tableName()
    {
        return 'notifications';
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
            $this->newField('type', 'string')->label('类型')
                ->inList()->required(),
            $this->newField('data', 'text')->label('内容')
                ->required(),
            $this->newField('read_at', 'timestamp')->label('阅读于')
                ->nullable()->factory($this->faker->dateTime)
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
