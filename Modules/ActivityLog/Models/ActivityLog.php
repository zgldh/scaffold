<?php namespace Modules\ActivityLog\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class ActivityLog
 * @package Modules\ActivityLog\Models
 * @version December 26, 2016, 5:37 am UTC
 */
class ActivityLog extends \Spatie\Activitylog\Models\Activity
{
    protected $table = 'z_activity_log';
    protected $appends = ['detail_string'];

    public function language($type)
    {
        return __($type);
    }

    /**
     * The
     * @return MorphTo
     */
    public function collector(): MorphTo
    {
        if (config('activitylog.subject_returns_soft_deleted_models')) {
            return $this->morphTo()->withTrashed();
        }
        return $this->morphTo();
    }

    /**
     * @return  \Illuminate\Database\Eloquent\Relations\MorphMany
     **/
    public function comments()
    {
        return $this->morphMany('Modules\Comment\Models\Comment', 'commentable');
    }

}
