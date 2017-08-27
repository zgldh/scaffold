<?php
/**
 * @var $MODEL \zgldh\Scaffold\Installer\Model\ModelDefinition
 * @var $field  \zgldh\Scaffold\Installer\Model\FieldDefinition
 */
$modelKebabCase = $MODEL->getKebabCase();
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
                                        \@current-change="onPageChange"
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
                                \@sort-change="onSortChange"
                                \@selection-change="onSelectionChange"
                                ref="table"
                        >
                            {!! $MODEL->generateDatatableColumns() !!}
                        </el-table>
                    </div>
                </div>
                <!-- /.box-body -->

                <div class="box-footer">
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
  import { mixin } from "resources/assets/js/commons/ListHelpers.js";

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
    methods: {
      onEditClick: function (row, column, $index, store) {
        return this.$router.push('/{{$modelKebabCase}}/' + row.id + '/edit');
      },
      onDeleteClick: function (row, column, $index, store) {
        return this._onDeleteClick({
          url: '/{{$modelKebabCase}}/' + row.id,
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
