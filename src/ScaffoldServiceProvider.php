<?php namespace zgldh\Scaffold;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use InfyOm\Generator\InfyOmGeneratorServiceProvider;
use Prettus\Repository\Providers\RepositoryServiceProvider;
use Spatie\Permission\PermissionServiceProvider;
use Yajra\Datatables\DatatablesServiceProvider;
use zgldh\Scaffold\Commands\ModuleCreateCommand;
use zgldh\Scaffold\Commands\ModuleInstallCommand;
use zgldh\Scaffold\Commands\PackageGenerator;
use zgldh\Scaffold\Commands\ScaffoldInitCommand;
use zgldh\Scaffold\Commands\ScaffoldPackagesCommand;
use zgldh\UploadManager\UploadManagerServiceProvider;

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
        }
    }
}