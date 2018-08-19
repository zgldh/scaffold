<?php namespace Modules\Setting\Sets;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractSet
{
    /**
     * @var Model
     */
    private $settingTarget = null;

    /**
     * AbstractSet constructor.
     * @param Model $settingTarget = null
     */
    public function __construct($settingTarget = null)
    {
        if ($settingTarget) {
            $this->setSettingTarget($settingTarget);
        }
    }

    /**
     * Register any application services.
     *
     * @return  array
     */
    abstract public function defaults();

    /**
     * @return null
     */
    public function getSettingTarget()
    {
        return $this->settingTarget;
    }

    /**
     * @param Model $settingTarget
     */
    public function setSettingTarget(Model $settingTarget)
    {
        $this->settingTarget = $settingTarget;
    }

    public function update($name, $value)
    {

    }

}
