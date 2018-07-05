<template>
  <div class="admin-list-page">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{$t('upload.title')}}
        <small>{{$t('global.terms.list')}}</small>
      </h1>
      <ol class="breadcrumb">
        <li>
          <router-link to="/"><i class="fa fa-dashboard"></i> {{$t('module_dashboard.title')}}</router-link>
        </li>
        <li>{{$t('upload.title')}}</li>
        <li class="active">{{$t('global.terms.list')}}</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <div class="buttons">
            <el-button type="danger" @click="onBundleDelete" icon="delete" :disabled="selectedItems.length==0">
              {{$t('global.terms.bundle_delete')}}
            </el-button>
          </div>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body datatable-loading-section">
          <div class="search">
            <el-form :inline="true" :model="searchForm" ref="searchForm">
              <el-form-item :label="$t('upload.fields.name')">
                <el-input v-model="searchForm.name" column="name" operator="like"></el-input>
              </el-form-item>
              <el-form-item :label="$t('upload.fields.description')">
                <el-input v-model="searchForm.description" column="description"
                          operator="like"></el-input>
              </el-form-item>
              <el-form-item :label="$t('upload.fields.type')">
                <el-input v-model="searchForm.type" column="type" operator="like"></el-input>
              </el-form-item>
              <el-form-item :label="$t('upload.fields.disk')">
                <el-input v-model="searchForm.disk" column="disk" operator="like"></el-input>
              </el-form-item>
              <el-form-item :label="$t('global.fields.created_at')">
                <el-date-picker
                        v-model="searchForm.created_at"
                        type="daterange"
                        clearable
                        column="created_at"
                        operator="range">
                </el-date-picker>
              </el-form-item>

              <el-form-item>
                <el-button-group>
                  <el-button type="primary" @click="onSubmitSearch">{{$t('global.terms.search_submit')}}</el-button>
                  <el-button type="button" @click="onResetSearch">{{$t('global.terms.search_reset')}}</el-button>
                </el-button-group>
              </el-form-item>
            </el-form>
          </div>

          <div class="datatable-container">
            <!-- 采用 datatables 标准-->
            <el-row class="tools">
              <el-col :span="4">
                <span class="page-size">{{$t('global.terms.page_size_show')}}
                <el-select v-model="pagination.pageSize" style="width: 80px"
                           @change="onPageSizeChange">
                  <el-option
                          v-for="item in pagination.pageSizeList"
                          :key="item.value"
                          :label="item.label"
                          :value="item.value">
                  </el-option>
                </el-select>
                  {{$t('global.terms.page_size_items')}}</span>
              </el-col>
              <el-col :span="12">
                <el-pagination
                        @current-change="onPageChange"
                        :current-page="pagination.currentPage"
                        :page-size="pagination.pageSize==-1?1:pagination.pageSize"
                        :layout="pagination.pageSize==-1?'total':'total, prev, pager, next, jumper'"
                        :total="pagination.totalCount">
                </el-pagination>
              </el-col>
              <el-col :span="8">
                <el-input class="auto-search" style="width: 200px;float: right;"
                          :placeholder="$t('global.terms.auto_search')"
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
                    @sort-change="onSortChange"
                    @selection-change="onSelectionChange"
                    ref="table"
            >
              <el-table-column
                      fixed
                      type="selection"
                      width="55">
              </el-table-column>
              <el-table-column
                      prop="name"
                      :label="$t('upload.fields.name')"
                      sortable="custom"
                      show-overflow-tooltip
                      width="180">
              </el-table-column>
              <el-table-column
                      prop="description"
                      :label="$t('upload.fields.description')"
                      sortable="custom"
                      show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                      prop="type"
                      :label="$t('upload.fields.type')"
                      sortable="custom"
                      show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                      prop="disk"
                      :label="$t('upload.fields.disk')"
                      sortable="custom"
                      show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                      prop="size"
                      :label="$t('upload.fields.size')"
                      sortable="custom"
                      show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                      prop="created_at"
                      :label="$t('global.fields.created_at')"
                      sortable="custom"
                      searchable="false"
                      show-overflow-tooltip>
              </el-table-column>
              <el-table-column
                      fixed="right"
                      :label="$t('global.terms.actions')"
                      width="150">
                <template slot-scope="scope">
                  <el-button-group>
                    <el-button @click="onViewClick(scope.row,scope.column,scope.$index,scope.store)" type="default"
                               size="small" icon="view" :title="$t('global.terms.view')"></el-button>
                    <el-button @click="onEditClick(scope.row,scope.column,scope.$index,scope.store)" type="default"
                               size="small" icon="edit" :title="$t('global.terms.edit')"></el-button>
                    <el-button @click="onDeleteClick(scope.row,scope.column,scope.$index,scope.store)" type="danger"
                               size="small" icon="delete" :title="$t('global.terms.delete')"></el-button>
                  </el-button-group>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
          <el-button type="danger" @click="onBundleDelete" icon="delete" :disabled="selectedItems.length==0">
            {{$t('global.terms.bundle_delete')}}
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

  export default {
    mixins: [
      mixin,
      loadModuleLanguage('module_upload')
    ],
    data: function () {
      let data = {
        resource: '/upload',
        datatablesParameters: {
          order: [{column: 'created_at', dir: 'desc'}],
        },
        searchForm: {
          name: null,
          description: null,
          disk: null,
          created_at: null
        }
      };
      return data;
    },
    methods: {
      onViewClick: function (row, column, $index, store) {
        return window.open(row.url);
      },
      onEditClick: function (row, column, $index, store) {
        return this.$router.push('/upload/' + row.id + '/edit');
      },
      onDeleteClick: function (row, column, $index, store) {
        return this._onDeleteClick({
          url: '/upload/' + row.id,
          params: {},
          confirmText: this.$i18n.t('global.delete_confirm.confirm_text'),
          messageText: this.$i18n.t('global.delete_confirm.complete_text'),
        }).then(result => {
          this.tableData.splice($index, 1);
          this.pagination.totalCount--;
        });
      },
      onBundleDelete: function () {
        return this.$confirm(this.$i18n.tc('global.delete_confirm.bundle_confirm_text', this.selectedItems.length, {count: this.selectedItems.length}),
                this.$i18n.t('global.terms.alert'), {
                  confirmButtonText: this.$i18n.t('global.terms.confirm'),
                  cancelButtonText: this.$i18n.t('global.terms.cancel'),
                  type: 'warning'
                }).then(() => {
          return this._onBundle('delete');
        }).then(result => {
          this.$message({
            type: 'success',
            message: this.$i18n.t('global.delete_confirm.complete_text')
          });
          return this.queryTableData();
        });
      },
    }
  };

</script>

<style lang="scss" scoped>

</style>
