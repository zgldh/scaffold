<?php namespace Modules\Setting\Repositories;

use Modules\Setting\Models\Setting;
use App\Scaffold\BaseRepository;
use Modules\Setting\Sets\AbstractSet;
use Prettus\Repository\Exceptions\RepositoryException;

class SettingRepository extends BaseRepository
{
    /**
     * @var [
     *          key=>value,
     *          key=>value,
     *      ] Array
     */
    private static $systemSettings = null;

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

    /**
     * TODO
     * @param AbstractSet $set
     */
    public function getSettingSet(AbstractSet $set)
    {

    }

    public function setSettingSet(AbstractSet $set)
    {

    }

    public function getSystemSettings($keys = [])
    {
        if (self::$systemSettings === null) {
            self::$systemSettings = $this->loadSystemSettings();
        }
        if (sizeof($keys) > 0) {
            return array_intersect_key(self::$systemSettings, array_flip($keys));
        }
        return self::$systemSettings;
    }

    public function getSystemSetting($key, $default = null)
    {
        $settings = $this->getSystemSettings([$key]);
        return array_get($settings, $key, $default);
    }

    public function setSystemSetting($key, $value)
    {
        if (is_array(self::$systemSettings)) {
            self::$systemSettings[$key] = $value;
        }
        try {
            $setting = $this->makeModel()->system()->where('name', $key)->firstOrNew();
            $setting->value = $value;
            $setting->save();
        } catch (RepositoryException $e) {
        }
    }

    private function loadSystemSettings()
    {
        try {
            $settings = $this->makeModel()->system()->pluck('value', 'name');
            dd($settings);
            return $settings;
        } catch (RepositoryException $e) {
            return [];
        }
    }
}
