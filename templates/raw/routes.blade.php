<?php
/**
 * @var $STARTER \zgldh\Scaffold\Installer\ModuleStarter
 * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
 */

$MODELS = $STARTER->getModels();
usort($MODELS,function($a,$b){
    return strlen($a->getRoute()) < strlen($b->getRoute());
});

echo '<?php' ?>


@foreach($MODELS as $MODEL)
Route::post('{{$MODEL->getRoute()}}/bundle','\{{$STARTER->getModuleNameSpace()}}\Controllers\{{$MODEL->getPascaleCase()}}Controller@bundle');
Route::resource('{{$MODEL->getRoute()}}', '\{{$STARTER->getModuleNameSpace()}}\Controllers\{{$MODEL->getPascaleCase()}}Controller');

@endforeach
