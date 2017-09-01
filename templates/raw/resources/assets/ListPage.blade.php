<?php
/**
 * @var $MODEL  \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
 */
$modelSnakeCase = $MODEL->getSnakeCase();
?>
<template>
  <div class="admin-list-page">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{$MODEL->getTitle()}}
        <small>列表</small>
      </h1>
      <ol class="breadcrumb">
        <li>
          <router-link to="/"><i class="fa fa-dashboard"></i> 总览</router-link>
        </li>
        <li>{{$MODEL->getTitle()}}</li>
        <li class="active">列表</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <div class="buttons">
            <el-button type="primary" @click="onCreate" icon="plus">添加</el-button>
            <el-button type="danger" @click="onBundleDelete" icon="delete"
                   :disabled="selectedItems.length==0">
              批量删除
            </el-button>
          </div>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body datatable-loading-section">
          <div class="search">
            {!! $MODEL->generateListSearchForm() !!}
          </div>

          <div class="datatable-container">
            <!-- 采用 datatables 标准-->
            <el-row class="tools">
              <el-col :span="4">
                <span class="page-size">显示
                <el-select v-model="pagination.pageSize" style="width: 80px"
                       @change="onPageSizeChange">
                  <el-option
                      v-for="item in pagination.pageSizeList"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value">
                  </el-option>
                </el-select>
                  项结果</span>
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
                      placeholder="模糊搜索"
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
  $prop = $field->getName();
  $label = $field->getLabel();
  $sortable = $field->isSortable() ? 'sortable="custom"' : ':sortable="false"';
  $searchable = $field->isNotSearchable() ? 'searchable="false"' : 'searchable="true"';
@endphp
              <el-table-column
                  prop="{{$prop}}"
                  label="{{$label}}"
                  {!! $sortable !!}
                  {!! $searchable !!}
                  show-overflow-tooltip>
              </el-table-column>

@php
  endforeach;
@endphp
              <el-table-column
                  fixed="right"
                  label="操作"
                  width="120">
                <template scope="scope">
                  <el-button-group>
                    <el-button @click="onEditClick(scope.row,scope.column,scope.$index,scope.store)" type="default"
                           size="small" icon="edit" title="编辑"></el-button>
                    <el-button @click="onDeleteClick(scope.row,scope.column,scope.$index,scope.store)" type="danger"
                           size="small" icon="delete" title="删除"></el-button>
                  </el-button-group>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
          <el-button type="primary" @click="onCreate" icon="plus">添加</el-button>
          <el-button type="danger" @click="onBundleDelete" icon="delete" :disabled="selectedItems.length==0">
            批量删除
          </el-button>
        </div>
      </div>

    </section>
    <!-- /.content -->
  </div>
</template>

<script type="javascript">
  import {mixin} from "resources/assets/js/commons/ListHelpers.js";

  export default {
    mixins: [mixin],
    data: function () {
      let data = {
        resource: '/{!! $MODEL->getRoute() !!}',
        datatablesParameters: {
          order: [{column: 'created_at', dir: 'desc'}],
        },
        searchForm: {!! json_encode(array_fill_keys(array_keys($MODEL->getSearches()),null), JSON_PRETTY_PRINT) !!}
      };
      return data;
    },
    computed:{
      @php
        $computes = $MODEL->generateComputes();
        foreach($computes as $compute):
          echo $compute.",\n";
        endforeach;
      @endphp
    },
    methods: {
      onCreate: function () {
        return this.$router.push('/{{$modelSnakeCase}}/create');
      },
      onEditClick: function (row, column, $index, store) {
        return this.$router.push('/{{$modelSnakeCase}}/' + row.id + '/edit');
      },
      onDeleteClick: function (row, column, $index, store) {
        return this._onDeleteClick({
          url: '/{{$modelSnakeCase}}/' + row.id,
          params: {},
          confirmText: '确认要删除吗？',
          messageText: '删除完毕'
        }).then(result => {
          this.tableData.splice($index, 1);
          this.pagination.totalCount--;
        });
      },
      onBundleDelete: function () {
        return this.$confirm("确认要删除 " + this.selectedItems.length + " 项么？", '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          return this._onBundle('delete');
        }).then(result => {
          this.$message({
            type: 'success',
            message: "删除完毕"
          });
          return this.queryTableData();
        });
      },
    }
  };

</script>

<style lang="scss" scoped>

</style>
