<?php
/**
 * @var $STARTER  \zgldh\Scaffold\Installer\ModuleStarter
 * @var $MODEL  \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
 */
$modelSnakeCase = $MODEL->getSnakeCase();
$modelCamelCase = $MODEL->getCamelCase();
$route = $MODEL->getRoute();
$languageNamespace = $STARTER->getLanguageNamespace();
$relationNames = $MODEL->getRelationNames();
?>
<template>
  <div class="{{$modelSnakeCase}}-editor-page admin-editor-page">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><?php echo "{{\$t('".$languageNamespace.".models.".$modelSnakeCase.".title')}}"; ?>
        <small v-if="form.id">@{{$t('scaffold.terms.edit')}}</small>
        <small v-else>@{{$t('scaffold.terms.create')}}</small>
      </h1>
      <ol class="breadcrumb">
        <li>
          <router-link to="/"><i class="fa fa-dashboard"></i> @{{$t('module_dashboard.title')}}</router-link>
        </li>
        <li>
          <router-link to="/{{$route}}/list"><?php echo "{{\$t('".$languageNamespace.".models.".$modelSnakeCase.".title')}}"; ?></router-link>
        </li>
        <li class="active" v-if="form.id">@{{$t('scaffold.terms.edit')}}</li>
        <li class="active" v-else>@{{$t('scaffold.terms.create')}}</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="box box-default">

        <div class="box-header with-border">
          <el-button type="default" @click="onCancel" icon="close">@{{$t('scaffold.terms.back')}}</el-button>
          <el-button type="primary" @click="onSave" icon="check" :loading="saving||loading">
            @{{$t('scaffold.terms.save')}}
          </el-button>
        </div>
        <!-- /.box-header -->

        <!-- form start -->
        <div class="box-body">
          <el-alert class="missing-errors" v-if="missingErrors.length" v-for="errorMessage in missingErrors"
                :key="errorMessage"
                :title="errorMessage" type="error" show-icon></el-alert>

          <!-- form start -->
          <el-form ref="form" :model="form" label-width="200px" v-loading="loading">
            <el-form-item :label="$t('scaffold.fields.id')" v-if="form.id">
              <el-input v-model="form.id" disabled></el-input>
            </el-form-item>
@php
            $fields = $MODEL->getFields();
            foreach ($fields as $field) {
                /**
                * @var $field     FieldDefinition
                * @var $baseField BaseField
                */
                $baseField = $field->getHtmlType();
                $fieldHtml = $baseField->html();
                echo $fieldHtml . "\n";
            }
@endphp
            <el-form-item :label="$t('scaffold.fields.created_at')" v-if="form.id">
              <el-input v-model="form.created_at" disabled></el-input>
            </el-form-item>
          </el-form>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
          <el-button type="default" @click="onCancel" icon="close">@{{$t('scaffold.terms.back')}}</el-button>
          <el-button type="primary" @click="onSave" icon="check" :loading="saving||loading">
            @{{$t('scaffold.terms.save')}}
          </el-button>
        </div>

      </div>

    </section>
    <!-- /.content -->

  </div>
</template>

<script type="text/javascript">
  import { mixin } from "resources/assets/js/commons/EditorHelper.js";
  import { loadModuleLanguage } from 'resources/assets/js/commons/LanguageHelper';
  import store from './store';

  export default  {
    mixins: [
      mixin,
      loadModuleLanguage('{{$languageNamespace}}')
    ],
    created: function(){
      this.registerStore('{{$modelCamelCase}}',store);
    },
    data: function () {
      return {
        form: {!! json_encode($MODEL->getDefaultValues(), JSON_PRETTY_PRINT) !!}
      };
    },
    components: {
@foreach($MODEL->getVueEditorComponents() as $component=>$path)
      '{!! $component !!}': () => System.import('{!! $path !!}'),
@endforeach
    },
    computed: {
      resource: function () {
        var resourceURL = '/{{$route}}'+ (this.form.id ? ('/' + this.form.id):'') ;
        return resourceURL{!! $relationNames?"+'?_with=".join(',', $relationNames)."'":'' !!};// + '?_with=roles,permissions';
      },
@include('zgldh.scaffold::raw.resources.assets.segments.computeds',['MODEL'=>$MODEL])
    },
    mounted: function () {
      this.loading = true;
      let loads = [];
      if (this.$route.params.id) {
        this.form.id = this.$route.params.id;
        loads.push(axios.get(this.resource));
      }

      Promise.all(loads).then(results => {
        this.form = results[0].data.data;
        this.loading = false;
      }).catch(err => {
        this.loading = false;
      });
    },
    methods: {
      onSave: function (event) {
        this._onSave(event).then(result => {
          this.$router.replace('/{{$route}}/' + result.data.data.id + '/edit');
          this.form = result.data.data;
        }).catch(err => {
        });
      },
      onCancel: function (event) {
        this.$router.back();
      },
//      onFileChange: function (event) {
//        this.form.file = event.target.files[0]; // TODO file upload
//      },
@include('zgldh.scaffold::raw.resources.assets.segments.actions',['MODEL'=>$MODEL])
    }
  };
</script>

<style lang="scss">
  .{{$modelSnakeCase}}-editor-page{
  
  }
</style>
