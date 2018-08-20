<?php namespace Modules\Setting\Bundles;

use ArrayAccess;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use IteratorAggregate;
use JsonSerializable;
use Modules\Setting\Models\Setting;

abstract class AbstractBundle implements Jsonable, JsonSerializable, ArrayAccess, Arrayable, Countable, IteratorAggregate
{
    /**
     * @var Model
     */
    private $settingTarget = null;

    private $data = null;

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
     * @return null|Model
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

    /**
     * TODO
     * @param $name
     * @param $value
     */
    public function update($name, $value)
    {

    }

    /**
     * TODO
     * @param $name
     * @return Setting
     */
    public function getSetting($name)
    {
    }

    /**
     * @param Collection $data
     */
    public function load(Collection $data)
    {
        $this->setData($data);
        $defaults = $this->defaults();
        foreach ($defaults as $key => $value) {
            if ($this->getData()->has($key) === false) {
                $this->getData()->put($key, $value);
            }
        }
    }

    /**
     * @return Collection
     */
    protected function getData()
    {
        if ($this->data === null) {
            $this->data = collect();
        }
        return $this->data;
    }

    protected function setData($data)
    {
        $this->data = $data;
    }

    public function toJson($options = 0)
    {
        return $this->getData()->toJson($options);
    }

    public function jsonSerialize()
    {
        return $this->getData();
    }

    public function offsetExists($offset)
    {
        return $this->getData()->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getData()->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->getData()->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->getData()->offsetUnset($offset);
    }

    public function toArray()
    {
        return $this->getData()->toArray();
    }

    public function count()
    {
        return $this->getData()->count();
    }

    public function getIterator()
    {
        return $this->getData()->getIterator();
    }
}
