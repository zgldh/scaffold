<?php
/**
* @var $STARTER  \zgldh\Scaffold\Installer\ModuleStarter
* @var $MODEL  \zgldh\Scaffold\Installer\Model\ModelDefinition
* @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
*/
$modelSnakeCase = $MODEL->getSnakeCase();
$states = $MODEL->generateStoreStates();
$mutations = $MODEL->generateStoreMutations();
$actions = $MODEL->generateStoreActions();
?>
import Vuex from 'vuex';
import {BuildAutoSearchQuery} from 'resources/assets/js/commons/Utils';

// Store functions
const store = new Vuex.Store({
  state: {
@php
    foreach($computes as $compute):
      echo $compute.",\n";
    endforeach;
@endphp
    {{--_categoryList: {--}}
      {{--"news": "新闻",--}}
      {{--"sport": "运动",--}}
    {{--},--}}
    {{--_statusList: {--}}
      {{--"1": "草稿",--}}
      {{--"2": "发布",--}}
    {{--},--}}
    {{--_createdByList: {}--}}
  },
  mutations: {
@php
    foreach($mutations as $mutation):
      echo $mutation.",\n";
    endforeach;
@endphp
    {{--_setCreatedByList: function (state, items) {--}}
      {{--state._createdByList = items;--}}
    {{--}--}}
  },
  actions: {
@php
    foreach($actions as $action):
      echo $action.",\n";
    endforeach;
@endphp
    {{--_queryCreatedByList: function ({commit}, term) {--}}
      {{--axios.get('/user?' + BuildAutoSearchQuery('name', term))--}}
        {{--.then(result => {--}}
          {{--var data = {};--}}
          {{--if (result.data && result.data.data) {--}}
            {{--result.data.data.forEach(item => {--}}
              {{--data[item.id] = item.name;--}}
            {{--});--}}
          {{--}--}}
          {{--commit('_setCreatedByList', data);--}}
        {{--});--}}
    {{--}--}}
  }
});
export default store;
