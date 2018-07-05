<?php
/**
 * @var $STARTER \zgldh\Scaffold\Installer\ModuleStarter
 * @var $MODEL   \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field   \zgldh\Scaffold\Installer\Model\FieldDefinition
 */
$models = $STARTER->getModels();
?>
export default [
<?php foreach($models as $MODEL):
$route = $MODEL->getRoute();
$modelName = $MODEL->getPascaleCase();
?>
  {path: '/{{$route}}/list', component: () => System.import('./{{$modelName}}/ListPage.vue')},
  {path: '/{{$route}}/create', component: () => System.import('./{{$modelName}}/EditorPage.vue')},
  {path: '/{{$route}}/:id/edit', component: () => System.import('./{{$modelName}}/EditorPage.vue')},
<?php endforeach; ?>
];