<?php
use App\Scaffold\Installer\Utils;
    /**
     * @var $MODEL \App\Scaffold\Installer\Model\ModelDefinition
     */
    $modelName = $MODEL->getPascaleCase();
    $modelCamelCase = $MODEL->getCamelCase();
    echo '<?php' ?> namespace {{$NAME_SPACE}}\Observers;

use {{$NAME_SPACE}}\Models\{{$modelName}};

/**
 * Reference: https://laravel.com/docs/5.5/eloquent#events
 **/
class {{$modelName}}Observer
{

    // public function retrieved({{$modelName}} {{$modelCamelCase}}){}

    // public function creating({{$modelName}} {{$modelCamelCase}}){}

    // public function created({{$modelName}} {{$modelCamelCase}}){}

    // public function updating({{$modelName}} {{$modelCamelCase}}){}

    // public function updated({{$modelName}} {{$modelCamelCase}}){}

    // public function saving({{$modelName}} {{$modelCamelCase}}){}

    // public function saved({{$modelName}} {{$modelCamelCase}}){}

    // public function deleting({{$modelName}} {{$modelCamelCase}}){}

    // public function deleted({{$modelName}} {{$modelCamelCase}}){}

    // public function restoring({{$modelName}} {{$modelCamelCase}}){}

    // public function restored({{$modelName}} {{$modelCamelCase}}){}
}
