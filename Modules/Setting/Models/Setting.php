<?php namespace Modules\Setting\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\HasActivity;

class Setting extends Model
{
    use HasActivity;

    public $table = 'z_settings';

    public $fillable = [
        "name",
        "value",
        "settable_id",
        "settable_type"
    ];

    protected static $logOnlyDirty = true;
    protected static $logAttributes = [
        "name",
        "value",
        "settable_id",
        "settable_type"
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var  array
     */
    protected $casts = [
        'name'          => 'string',
        'value'         => 'array',
        'settable_id'   => 'integer',
        'settable_type' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var  array
     */
    public static $rules = [
        'name'          => 'required',
        'value'         => 'required',
        'settable_id'   => '',
        'settable_type' => '',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function settingTarget()
    {
        return $this->morphTo('settable');
    }
}
