<?php namespace App\Scaffold;

use App\Scaffold\Commands\AddAPI;
use App\Scaffold\Commands\AddModule;
use App\Scaffold\Commands\AddModel;
use App\Scaffold\Commands\DumpLanguages;
use App\Scaffold\Commands\FromTable;
use App\Scaffold\Commands\ScaffoldInit;
use App\Scaffold\Commands\UpdatePermissions;
use Illuminate\Support\ServiceProvider;

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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->loadViewsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'templates', 'scaffold');
    }

    private function registerCommands()
    {
        $this->app->singleton('scaffold.init', function ($app) {
            return new ScaffoldInit();
        });
        $this->app->singleton('scaffold.api', function ($app) {
            return new AddAPI();
        });
        $this->app->singleton('scaffold.module', function ($app) {
            return new AddModule();
        });
        $this->app->singleton('scaffold.model', function ($app) {
            return new AddModel();
        });
        $this->app->singleton('lang.dump', function ($app) {
            return new DumpLanguages();
        });

        $this->commands([
            'scaffold.init',
            'scaffold.api',
            'scaffold.module',
            'scaffold.model',
            'lang.dump',
        ]);
    }
}