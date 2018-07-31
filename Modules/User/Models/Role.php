<?php namespace Modules\User\Models;

use Spatie\Activitylog\Traits\HasActivity;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    use HasActivity;

    public $table = 'z_roles';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $connection = 'mysql';

    protected $tempLabel = null;

    public $fillable = [
        'name',
        'label',
        'guard_name',
    ];

    protected static $logOnlyDirty = true;
    protected static $logAttributes = [
        'name',
        'label',
        'guard_name',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'name'        => 'string',
        'label'       => 'string',
        'guard_name'  => 'string',
        'permissions' => 'array'
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

    public function getLabelAttribute($label)
    {
        return $this->tempLabel ?: __('role.roles.' . $this->snake_case_name);
    }

    public function setLabelAttribute($label)
    {
        $this->attributes['label'] = $this->attributes['name'];
    }

    public function getSnakeCaseNameAttribute()
    {
        return str_replace('-', '_', snake_case($this->attributes['name']));
    }

    public function setTempLabel($label)
    {
        $this->tempLabel = $label;
    }
}
