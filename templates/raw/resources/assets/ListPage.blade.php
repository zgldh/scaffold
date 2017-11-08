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

?>
<template>
  <div class="{{$modelSnakeCase}}-list-page admin-list-page">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><?php echo "{{\$t('".$languageNamespace.".models.".$modelSnakeCase.".title')}}"; ?>
        <small>@{{$t('scaffold.terms.list')}}</small>
      </h1>
      <ol class="breadcrumb">
        <li>
          <router-link to="/"><i class="fa fa-dashboard"></i> @{{$t('module_dashboard.title')}}</router-link>
        </li>
        <li><?php echo "{{\$t('".$languageNamespace.".models.".$modelSnakeCase.".title')}}"; ?></li>
        <li class="active">@{{$t('scaffold.terms.list')}}</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <div class="buttons">
            <el-button type="primary" @click="onCreate" icon="plus">@{{$t('scaffold.terms.create')}}</el-button>
            <el-button type="danger" @click="onBundleDelete" icon="delete"
                   :disabled="selectedItems.length==0">
              @{{$t('scaffold.terms.bundle_delete')}}
            </el-button>
          </div>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body datatable-loading-section">
          <div class="search">
            <el-form :inline="true" :model="searchForm" ref="searchForm">
@php
              $searches = $MODEL->getSearches();
              foreach ($searches as $fieldName => $searchType) {
                /**
                * @var $searchType BaseField
                */
                $fieldHtml = $searchType->searchHtml();
                echo $fieldHtml . "\n";
              }
@endphp
              <el-form-item>
                <el-button-group>
                  <el-button type="primary" @click="onSubmitSearch">@{{$t('scaffold.terms.search_submit')}}</el-button>
                  <el-button type="button" @click="onResetSearch">@{{$t('scaffold.terms.search_reset')}}</el-button>
                </el-button-group>
              </el-form-item>
            </el-form>
          </div>

          <div class="datatable-container">
            <!-- 采用 datatables 标准-->
            <el-row class="tools">
              <el-col :span="4">
                <span class="page-size">@{{$t('scaffold.terms.page_size_show')}}
                <el-select v-model="pagination.pageSize" style="width: 80px"
                       @change="onPageSizeChange">
                  <el-option
                      v-for="item in pagination.pageSizeList"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value">
                  </el-option>
                </el-select>
                  @{{$t('scaffold.terms.page_size_items')}}</span>
              </el-col>
              <el-col :span="12">
                <el-pagination
                    {!! '@current-change="onPageChange"' !!}
                    :current-page="pagination.currentPage"
                    :page-size="pagination.pageSize==-1?1:pagination.pageSize"
                    :layout="pagination.pageSize==-1?'total':'total, prev, pager, next, jumper'"
                    :total="pagination.totalCount">
                </el-pagination>
              </el-col>
              <el-col :span="8">
                <el-input class="auto-search" style="width: 200px;float: right;"
                      :placeholder="$t('scaffold.terms.auto_search')"
                      v-model="datatablesParameters.search.value"
                      :icon="datatablesParameters.search.value?'close':'search'"
                      :on-icon-click="onAutoSearchIconClick"
                      @change="onAutoSearchChanged">
                </el-input>
              </el-col>
            </el-row>

          </div>
          <div class="datatable" style="margin-top:1em;">
            <el-table
                :data="tableData"
                border
                style="width: 100%"
                max-height="500"
                :default-sort="defaultSort"
                {!! '@sort-change="onSortChange"' !!}
                {!! '@selection-change="onSelectionChange"' !!}
                ref="table"
            >
              <el-table-column
                  fixed
                  type="selection"
                  width="55">
              </el-table-column>

@php
  $fields = $MODEL->getFields();
  foreach ($fields as $field):
  if (!$field->isInIndex()):
    continue;
  endif;
  $relationship = $field->getRelationship();
  if($relationship){
    $searchColumns = \zgldh\Scaffold\Installer\Utils::getTargetModelSearchColumns($relationship[0]);
  }
  $prop = $field->getName();
  $label = $field->getFieldLang(true);
  $sortable = $field->isSortable() ? 'sortable="custom"' : ':sortable="false"';
  $searchable = $field->isNotSearchable() ? 'searchable="false"' : 'searchable="true"';
@endphp
              <el-table-column
                  prop="{{$prop}}"
                  :label="{!! $label !!}"
                  {!! $sortable !!}
                  {!! $searchable !!}
                  show-overflow-tooltip>
                @if($relationship)
                  <template slot-scope="scope">
                    @foreach($searchColumns as $index=>$searchColumn)
                    @php
                      $rowRelation = 'scope.row.'.camel_case(basename($relationship[0]));
                    @endphp
                    <span><?php echo "{{{$rowRelation}?{$rowRelation}.{$searchColumn}:''"; ?>}}</span>{{$index!=count($searchColumns)-1?',':''}}
                    @endforeach
                  </template>
                @elseif($field->isRenderFromComputed())
                  <template slot-scope="scope">
                    <span><?php echo '{{'; ?> {{$field->getHtmlType()->getComputedPropertyName()}}[scope.row.{{$prop}}] <?php echo '}}'; ?></span>
                  </template>
                @endif
              </el-table-column>

@php
  endforeach;
@endphp
              <el-table-column
                  fixed="right"
                  :label="$t('scaffold.terms.actions')"
                  width="120">
                <template slot-scope="scope">
                  <el-button-group>
                    <el-button @click="onEditClick(scope.row,scope.column,scope.$index,scope.store)" type="default"
                           size="small" icon="edit" :title="$t('scaffold.terms.edit')"></el-button>
                    <el-button @click="onDeleteClick(scope.row,scope.column,scope.$index,scope.store)" type="danger"
                           size="small" icon="delete" :title="$t('scaffold.terms.delete')"></el-button>
                  </el-button-group>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
          <el-button type="primary" @click="onCreate" icon="plus">@{{$t('scaffold.terms.create')}}</el-button>
          <el-button type="danger" @click="onBundleDelete" icon="delete" :disabled="selectedItems.length==0">
            @{{$t('scaffold.terms.bundle_delete')}}
          </el-button>
        </div>
      </div>

    </section>
    <!-- /.content -->
  </div>
</template>

<script type="text/javascript">
  import { mixin } from "resources/assets/js/commons/ListHelpers.js";
  import { loadModuleLanguage } from 'resources/assets/js/commons/LanguageHelper';
  import store from './store';

  export default {
    mixins: [
      mixin,
      loadModuleLanguage('{{$languageNamespace}}')
    ],
    created: function(){
      this.registerStore('{{$modelCamelCase}}',store);
    },
    data: function () {
      let data = {
        resource: '/{!! $MODEL->getRoute($MODEL->getRelationNames()) !!}',
        datatablesParameters: {
          order: [{column: 'created_at', dir: 'desc'}],
        },
        searchForm: {!! json_encode(array_fill_keys(array_keys($MODEL->getSearches()),null), JSON_PRETTY_PRINT) !!}
      };
      return data;
    },
    computed:{
@include('zgldh.scaffold::raw.resources.assets.segments.computeds',['MODEL'=>$MODEL])
    },
    methods: {
      onCreate: function () {
        return this.$router.push('/{{$route}}/create');
      },
      onEditClick: function (row, column, $index, store) {
        return this.$router.push('/{{$route}}/' + row.id + '/edit');
      },
      onDeleteClick: function (row, column, $index, store) {
        return this._onDeleteClick({
          url: '/{{$route}}/' + row.id,
          params: {},
          confirmText: this.$i18n.t('scaffold.delete_confirm.confirm_text'),
          messageText: this.$i18n.t('scaffold.delete_confirm.complete_text'),
        }).then(result => {
          this.tableData.splice($index, 1);
          this.pagination.totalCount--;
        });
      },
      onBundleDelete: function () {
        return this.$confirm(this.$i18n.tc('scaffold.delete_confirm.bundle_confirm_text', this.selectedItems.length, {count:this.selectedItems.length}),
          this.$i18n.t('scaffold.terms.alert'), {
          confirmButtonText: this.$i18n.t('scaffold.terms.confirm'),
          cancelButtonText: this.$i18n.t('scaffold.terms.cancel'),
          type: 'warning'
        }).then(() => {
          return this._onBundle('delete');
        }).then(result => {
          this.$message({
            type: 'success',
            message: this.$i18n.t('scaffold.delete_confirm.complete_text'),
          });
          return this.queryTableData();
        });
      },
@include('zgldh.scaffold::raw.resources.assets.segments.actions',['MODEL'=>$MODEL])
    }
  };

</script>

<style lang="scss">
  .{{$modelSnakeCase}}-list-page{
  
  }
</style>
