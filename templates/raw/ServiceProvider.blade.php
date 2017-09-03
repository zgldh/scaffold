<?php
use zgldh\Scaffold\Installer\Utils;
/**
 * @var $STARTER \zgldh\Scaffold\Installer\ModuleStarter
 * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
 */
$languageNamespace = $STARTER->getLanguageNamespace();
echo '<?php' ?> namespace {{$NAME_SPACE}};

use Illuminate\Support\ServiceProvider;

class {{$MODULE_NAME}}ServiceProvider extends ServiceProvider
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
        $this->loadTranslationsFrom(resource_path('lang/vendor/{!! $languageNamespace !!}'), '{!! $languageNamespace !!}');
        $this->loadViewsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views',
        '{{$NAME_SPACE}}');
    }
}