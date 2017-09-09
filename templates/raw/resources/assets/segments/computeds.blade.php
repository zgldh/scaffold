<?php
/**
 * @var $STARTER  \zgldh\Scaffold\Installer\ModuleStarter
 * @var $MODEL  \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
 */
$modelSnakeCase = $MODEL->getSnakeCase();
$route = $MODEL->getRoute();
?>
@foreach($MODEL->getFields() as $field)
@php
  $htmlType = $field->getHtmlType();
@endphp
@if($field->getRelationship() ||  $htmlType->getOptions())
      {{$htmlType->getComputedPropertyName()}}() {
        return this.$store.state.{{$htmlType->getComputedPropertyName()}};
      },
@endif
@endforeach