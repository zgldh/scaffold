<?php
/**
 * @var $STARTER \App\Scaffold\Installer\ModelStarter
 * @var $MODEL   \App\Scaffold\Installer\Model\ModelDefinition
 */

$MODEL = $STARTER->getModel(); ?>
$api->post('{{$MODEL->getRoute()}}/bundle', '\{{$STARTER->getModuleNameSpace()}}\Controllers\{{$MODEL->getPascaleCase()}}Controller@bundle');
$api->resource('{{$MODEL->getRoute()}}', '\{{$STARTER->getModuleNameSpace()}}\Controllers\{{$MODEL->getPascaleCase()}}Controller');
