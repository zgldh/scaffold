<?php
/**
* @var $STARTER  \App\Scaffold\Installer\ModuleStarter
* @var $MODEL  \App\Scaffold\Installer\Model\ModelDefinition
* @var $field  \App\Scaffold\Installer\Model\FieldDefinition
*/
$modelSnakeCase = $MODEL->getSnakeCase();

?>
import { BuildAutoSearchQuery } from 'resources/assets/js/commons/Utils';

// Store functions
const store = {
  state: {
@foreach($MODEL->getFields() as $field)
@php
        $htmlType = $field->getHtmlType();
@endphp
@if($field->getRelationship())
    {{$htmlType->getComputedPropertyName()}}: [],
@elseif($htmlType->getOptions())
    {{$htmlType->getComputedPropertyName()}}: {!! json_encode($htmlType->getOptions(), JSON_UNESCAPED_UNICODE) !!},
@else
@endif
@endforeach
  },
  mutations: {
@foreach($MODEL->getFields() as $field)
@if($field->getRelationship())
@php
    $htmlType = $field->getHtmlType();
@endphp
    {{$htmlType->getStoreMutationName()}}: function (state, items) {
      state.{{$htmlType->getStoreStateName()}} = items;
    },
@else
@endif
@endforeach
  },
  actions: {
@foreach($MODEL->getFields() as $field)
@if($relationship = $field->getRelationship())
@php
    $htmlType = $field->getHtmlType();
    $targetModel = @$relationship[0];
    if(!$targetModel){
        continue;
    }
    $relationRoute = \App\Scaffold\Installer\Utils::generateTargetModelRoute($targetModel);
    $searchColumns = \App\Scaffold\Installer\Utils::getTargetModelSearchColumns($targetModel);
@endphp
    {{$htmlType->getStoreActionName()}}: function ({commit}, term) {
      axios.get('/{!! $relationRoute !!}?' + BuildAutoSearchQuery({!! json_encode($searchColumns) !!}, term))
        .then(result => {
          var data = [];
          if (result.data && result.data.data) {
            data = result.data.data;
          }
          commit('{{$htmlType->getStoreMutationName()}}', data);
        });
    },
@else
@endif
@endforeach
  }
};
export default store;
