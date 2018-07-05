<?php namespace Modules\ActivityLog\Repositories;

use App\Scaffold\BaseRepository;
use Modules\ActivityLog\Models\ActivityLog;

class ActivityLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'subject',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ActivityLog::class;
    }
}
