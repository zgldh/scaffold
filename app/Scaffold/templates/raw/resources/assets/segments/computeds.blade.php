<?php
/**
 * @var $STARTER  \App\Scaffold\Installer\ModuleStarter
 * @var $MODEL  \App\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \App\Scaffold\Installer\Model\Field
 */
$modelSnakeCase = $MODEL->getSnakeCase();
$modelCamelCase = $MODEL->getCamelCase();
$route = $MODEL->getRoute();
?>
@foreach($MODEL->getFields() as $field)
@php
  $htmlType = $field->getHtmlType();
@endphp
@if($field->getRelationship() ||  $htmlType->getOptions())
      {{$htmlType->getComputedPropertyName()}}() {
        return this.$store.state.{{$modelCamelCase}}.{{$htmlType->getComputedPropertyName()}};
      },
@endif
@endforeach