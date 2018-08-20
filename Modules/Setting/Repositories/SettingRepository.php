<?php namespace Modules\Setting\Repositories;

use Modules\Setting\Models\Setting;
use App\Scaffold\BaseRepository;
use Modules\Setting\Bundles\AbstractBundle;
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
     * Get a setting bundle
     * @param AbstractBundle $bundle
     * @return AbstractBundle
     */
    public function getBundle(AbstractBundle $bundle)
    {
        try {
            $query = $this->makeModel()->newQuery();
        } catch (RepositoryException $e) {
            $query = Setting::query();
        }
        $settingTarget = $bundle->getSettingTarget();
        if ($settingTarget) {
            $query = $query->where([
                ['settable_id', '=', $settingTarget->getKeyName()],
                ['settable_type', '=', $settingTarget->getMorphClass()]
            ]);
        } else {
            $query = $query->whereNull('settable_id')->whereNull('settable_type');
        }

        $settings = $query->pluck('value', 'name');
        $bundle->load($settings);
        return $bundle;
    }

    public function setBundle(AbstractBundle $bundle)
    {

    }

    public function updateByBundle(AbstractBundle $bundle, $name, $value)
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
}
