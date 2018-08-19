<?php

use Modules\Setting\Repositories\SettingRepository;

if (!function_exists('setting')) {
    /**
     * Get / set the setting value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string $key
     * @param  mixed $default
     * @return mixed|\Illuminate\Config\Repository
     */
    function setting($key = null, $default = null)
    {
        $repository = app(SettingRepository::class);
        if (is_null($key)) {
            return $repository;
        }

        if (is_array($key)) {
            foreach ($key as $name => $value) {
                $repository->setSystemSetting($name, $value);
            }
        } else {
            return $repository->getSystemSetting($key, $default);
        }
    }
}
