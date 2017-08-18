<?php namespace zgldh\Scaffold;

use Illuminate\Support\ServiceProvider;
use zgldh\Scaffold\Commands\ModuleCreateCommand;
use zgldh\Scaffold\Commands\ModuleInstallCommand;
use zgldh\Scaffold\Commands\ScaffoldInitCommand;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 11/14/2016
 * Time: 2:00 PM
 */
class ScaffoldServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->registerCommands();
        $this->registerOtherProviders();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->loadViewsFrom(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'templates',
            'zgldh.scaffold');
        $this->loadTranslationsFrom(resource_path('lang/vendor/scaffold'), 'scaffold');
    }

    private function registerCommands()
    {
        $this->app->singleton('zgldh.scaffold.init', function ($app) {
            return new ScaffoldInitCommand();
        });
        $this->app->singleton('zgldh.module.create', function ($app) {
            return new ModuleCreateCommand();
        });
        $this->app->singleton('zgldh.module.install', function ($app) {
            return new ModuleInstallCommand();
        });

        $this->commands([
            'zgldh.scaffold.init',
            'zgldh.module.create',
            'zgldh.module.install',
        ]);
    }

    private function registerOtherProviders()
    {
        if ($this->app->environment() == 'local') {
            $this->app->register(\Laracasts\Generators\GeneratorsServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}