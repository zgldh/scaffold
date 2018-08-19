<?php namespace Modules\Setting;

use Illuminate\Support\ServiceProvider;
use Modules\Setting\Repositories\SettingRepository;

class SettingServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return  void
     */
    public function register()
    {
        //
        $this->app->singleton(SettingRepository::class, function ($app) {
            return new SettingRepository($app);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return  void
     */
    public function boot()
    {
        //
        $this->loadViewsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views',
            'Modules\Setting');
    }
}