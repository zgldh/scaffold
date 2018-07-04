<?php
/**
 * @var $STARTER \App\Scaffold\Installer\ModuleStarter
 * @var $MODEL   \App\Scaffold\Installer\Model\ModelDefinition
 * @var $field   \App\Scaffold\Installer\Model\Field
 */
$frontendRoute = $MODEL->getFrontEndRoutePrefix();
$modelName = $MODEL->getPascaleCase();
$moduleName = $MODEL->getModuleName();
$model_snake_case = $MODEL->getSnakeCase();
?>
{
  path: '{{$frontendRoute}}',
  component: Layout,
  redirect: '{{$frontendRoute}}/list',
  name: '{{$modelName}}',
  permissions: ['{{$modelName}}@index'],
  meta: { title: () => i18n.t('{{$model_snake_case}}.title'), icon: 'fa-table' },
  children: [
    {
      path: 'list',
      name: '{{$modelName}} List',
      permissions: ['{{$modelName}}@index'],
      component: () => import('@/views/{{$moduleName}}/{{$modelName}}/List'),
      meta: { title: () => i18n.t('global.terms.list'), icon: 'fa-table' }
    },
    {
      hidden: true,
      path: ':id/edit',
      name: 'Edit {{$modelName}}',
      permissions: ['{{$modelName}}@update'],
      component: () => import('@/views/{{$moduleName}}/{{$modelName}}/Editor'),
      meta: { title: () => i18n.t('global.terms.edit'), icon: 'fa-table' }
    },
    {
      hidden: true,
      path: 'create',
      name: 'Create {{$modelName}}',
      permissions: ['{{$modelName}}@store'],
      component: () => import('@/views/{{$moduleName}}/{{$modelName}}/Editor'),
      meta: { title: () => i18n.t('global.terms.create'), icon: 'fa-table' }
    }
  ]
}