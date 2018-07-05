<?php namespace Modules\User\Models;

use Spatie\Permission\Models\Permission as BasePermission;
use Modules\ActivityLog\Traits\LogsActivity;

class Permission extends BasePermission
{
    use LogsActivity;

    public $table = 'permissions';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $connection = 'mysql';

    protected $tempLabel = null;

    public $fillable = [
        'name',
        'label',
        'guard_name'
    ];

    protected static $logAttributes = [
        'name',
        'label',
        'guard_name'
    ];

    protected $appends = [
//        'module_name'
        'model',
        'action'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'name'       => 'string',
        'label'      => 'string',
        'guard_name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name'  => 'required',
        'label' => 'required'
    ];

    public function getIsDefaultActionAttribute()
    {
        $defaultActions = array_map(function ($item) {
            return '@' . $item;
        }, array_keys(__('permission.default_actions')));
        return ends_with($this->name, $defaultActions);
    }

    public function getModelAttribute()
    {
        return substr($this->name, 0, strpos($this->name, '@'));
    }

    public function getActionAttribute()
    {
        return substr($this->name, strpos($this->name, '@') + 1);
    }

    public function setModelAttribute($value)
    {
        // TODO
    }

    public function setActionAttribute($value)
    {
        // TODO
    }

    public function getLabelAttribute($label)
    {
        if ($this->tempLabel) {
            return $this->tempLabel;
        }

        if ($this->is_default_action) {
            return __('permission.default_actions.' . snake_case($this->action));
        }
        return __(snake_case($this->model) . '.permissions.' . snake_case($this->action));
    }

    public function setLabelAttribute($label)
    {
        $this->attributes['label'] = $this->attributes['name'];
    }

    public function getModuleNameAttribute()
    {
        $name = "{$this->name}";
        $nameItems = explode('@', $name);
        if (isset($nameItems[1])) {
            $module = __('permission.modules.' . $nameItems[0]);
            return strpos($module, 'permission.actions.') !== false ? $name : $module;
        } else {
            return $name;
        }
    }

    public function setTempLabel($label)
    {
        $this->tempLabel = $label;
    }
}
