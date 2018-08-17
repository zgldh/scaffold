<?php namespace Modules\Setting;

use App\Scaffold\Installer\ModelStarter;

class SettingStarter extends ModelStarter
{

    /**
     * ModelNameLikeThis
     * @return string
     */
    protected function modelName()
    {
        return 'Setting';
    }

    /**
     * table_name_like_this
     * @return string
     */
    protected function tableName()
    {
        return 'settings';
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
            $this->newField('name', 'string')->label('内部编号')
                ->inList()->required()->factory($this->faker->unique()),
            $this->newField('value', 'json')->label('取值')
                ->inList()->defaultValue('""')->required()
                ->factory($this->faker->realText()),
            $this->newField('type', 'string')->label('类型')
                ->inList()->defaultValue('system')
                ->validations('in:system,settable')
                ->factory($this->faker->randomElement(['system', 'settable'])),
            $this->newField('settable_id', 'integer')->label('可配置对象ID')
                ->nullable(),
            $this->newField('settable_type', 'string')->label('可配置对象类型')
                ->nullable()
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
        return false;
    }
}
