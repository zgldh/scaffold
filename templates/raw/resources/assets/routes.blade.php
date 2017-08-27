<?php
/**
 * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
 */
?>
export default [
  {path: '/upload/list', component: () => System.import('./ListPage.vue')},
  // {path: '/upload/create', component: () => System.import('./EditorPage.vue')},
  {path: '/upload/:id/edit', component: () => System.import('./EditorPage.vue')},
];