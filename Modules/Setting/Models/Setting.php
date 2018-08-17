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
        "type",
        "settable_id",
        "settable_type"
    ];

    protected static $logOnlyDirty = true;
    protected static $logAttributes = [
        "name",
        "value",
        "type",
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
        'type'          => 'string',
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
        'type'          => 'in:system,settable',
        'settable_id'   => '',
        'settable_type' => '',
    ];
}
