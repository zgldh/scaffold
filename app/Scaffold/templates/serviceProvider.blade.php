<?php
/**
 * @var $moduleNameSpace
 * @var $moduleName
 */
echo '<?php' ?> namespace {{$moduleNameSpace}};

use Illuminate\Support\ServiceProvider;

class {{$moduleName}}ServiceProvider extends ServiceProvider
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
    }
}