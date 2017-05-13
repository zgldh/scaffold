<?php namespace zgldh\Scaffold;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use InfyOm\Generator\InfyOmGeneratorServiceProvider;
use Prettus\Repository\Providers\RepositoryServiceProvider;
use Spatie\Permission\PermissionServiceProvider;
use Yajra\Datatables\DatatablesServiceProvider;
use zgldh\Scaffold\Commands\PackageGenerator;
use zgldh\Scaffold\Commands\ScaffoldInitCommand;
use zgldh\Scaffold\Commands\ScaffoldPackagesCommand;
use zgldh\Scaffold\Commands\UserCreateCommand;
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
        $this->app->singleton('zgldh.scaffold.init', function ($app) {
            return new ScaffoldInitCommand();
        });
        $this->app->singleton('zgldh.package', function ($app) {
            return new PackageGenerator();
        });
        $this->app->singleton('zgldh.user.create', function ($app) {
            return new UserCreateCommand();
        });

        $this->commands([
            'zgldh.scaffold.init',
            'zgldh.package',
            'zgldh.user.create'
        ]);

        if ($this->app->environment() == 'local') {
            $this->app->register(\Laracasts\Generators\GeneratorsServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $configPath = __DIR__ . '/../templates/init';

        $this->publishes([
            $configPath => base_path('/'),
        ]);

        \Blade::directive('dist', function ($expression) {
            $file = trim($expression, "('')") . '.js';
            return '<script src="/dist/' . $file . '"></script>';
        });
    }
}