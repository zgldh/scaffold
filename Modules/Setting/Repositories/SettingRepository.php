<?php namespace Modules\Setting\Repositories;

use Modules\Setting\Models\Setting;
use App\Scaffold\BaseRepository;

class SettingRepository extends BaseRepository
{
    /**
     * @var  array
     */
    protected $fieldSearchable = [
        "name",
        "value",
        "type",
        "settable_id",
        "settable_type"
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Setting::class;
    }
}
