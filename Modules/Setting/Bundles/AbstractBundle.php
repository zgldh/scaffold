<?php namespace Modules\Setting\Bundles;

use ArrayAccess;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use IteratorAggregate;
use JsonSerializable;
use Modules\Setting\Exceptions\NameDoesNotExist;
use Modules\Setting\Models\Setting;
use Modules\Setting\Repositories\SettingRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

abstract class AbstractBundle implements Jsonable, JsonSerializable, ArrayAccess, Arrayable, Countable, IteratorAggregate
{
    /**
     * @var Model
     */
    private $settingTarget = null;

    private $data = null;

    private $enableSetter = true;

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

    abstract public function alias();

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
     * 更新本 Bundle 的一个配置项
     * @param $name
     * @param $value
     */
    public function update($name, $value)
    {
        /** @var SettingRepository $repository */
        $repository = app(SettingRepository::class);
        $repository->updateByBundle($this, $name, $value);
    }

    /**
     * 将本 Bundle 当前数据持久化储存起来
     */
    public function persist()
    {
        /** @var SettingRepository $repository */
        $repository = app(SettingRepository::class);
        $repository->setBundle($this);
    }

    /**
     * Get a specific setting. If not exist, create the default one and return it.
     * @param $name
     * @return Setting
     */
    public function getSetting($name)
    {
        if ($this->hasName($name) === false) {
            throw NameDoesNotExist::named($name, $this->alias());
        }
        /** @var SettingRepository $repository */
        $repository = app(SettingRepository::class);
        $query = $repository->getNewQuery();
        $query = $this->makeQueryFiltered($query);
        $setting = $query->where('name', $name)->first();
        if (!$setting) {
            $defaultValue = $this->getDefault($name);
            $this->update($name, $defaultValue);
            $setting = $query->first();
        }
        return $setting;
    }

    /**
     * 这个Bundle是否拥有该 $name 的选项
     * @param $name
     * @return bool
     */
    public function hasName($name)
    {
        return key_exists($name, $this->defaults());
    }

    /**
     * 得到一个配置项 $name 的默认值
     * @param $name
     * @return mixed
     */
    public function getDefault($name)
    {
        $defaults = $this->defaults();
        return $defaults[$name];
    }

    /**
     * 使一个 $query 附加上 settable 过滤条件
     * @param $query
     * @return mixed
     */
    public function makeQueryFiltered($query)
    {
        $settingTarget = $this->getSettingTarget();
        if ($settingTarget) {
            $query = $query->where([
                ['settable_id', '=', $settingTarget->getKeyName()],
                ['settable_type', '=', $settingTarget->getMorphClass()]
            ]);
        } else {
            $query = $query->whereNull('settable_id')->whereNull('settable_type');
        }
        return $query;
    }

    /**
     * @param Collection $data
     */
    public function load(Collection $data)
    {
        $this->setData($data);
        $defaults = $this->defaults();
        foreach ($defaults as $name => $value) {
            if ($this->getData()->has($name) === false) {
                $this->getData()->put($name, $value);
            }
        }
    }

    public function getKeys()
    {
        return array_keys($this->defaults());
    }

    /**
     * Get merged name=>value pairs
     * @return array
     */
    public function getValues()
    {
        $result = [];
        $defaults = $this->defaults();
        foreach ($defaults as $name => $value) {
            if ($this->getData()->has($name)) {
                $result[$name] = $this->getData()->get($name);
            } else {
                $result[$name] = $value;
            }
        }
        return $result;
    }

    private function getFinalValue($name, $value)
    {
        $setterMethod = 'set' . studly_case($name);
        if (method_exists($this, $setterMethod)) {
            $oldValue = $this[$name];
            return call_user_func_array([$this, $setterMethod], [$value, $oldValue]);
        }
        return $value;
    }

    /////////////////////////////////////////////////////////////////////////

    /**
     * @return Collection
     */
    protected function getData()
    {
        if ($this->data === null) {
            $this->data = collect($this->defaults());
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
        $finalValue = $this->enableSetter ? $this->getFinalValue($offset, $value) : $value;
        return $this->getData()->offsetSet($offset, $finalValue);
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

    /**
     * @param bool $enableSetter
     * @return AbstractBundle
     */
    public function setEnableSetter(bool $enableSetter): AbstractBundle
    {
        $this->enableSetter = $enableSetter;
        return $this;
    }
}
