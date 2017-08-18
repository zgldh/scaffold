<?php namespace $NAME$\Dashboard;

use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->loadViewsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views',
            '$NAME$\Dashboard');
        $this->loadTranslationsFrom(resource_path('lang/vendor/module-dashboard'), 'module-dashboard');
    }
}