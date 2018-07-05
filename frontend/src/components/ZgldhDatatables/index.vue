<template>
  <div class="zgldh-datatables" v-loading="loading">
    <el-row class="tools">
      <el-col :span="12">
        <cell-actions v-if="multipleActions.length>0"
                      size="medium" type="primary"
                      :target="selectedItems"
                      :actions="multipleActions"></cell-actions>
      </el-col>
      <el-col :span="12" class="right-tools">
        <el-input class="auto-search"
                  :placeholder="$t('global.terms.auto_search')"
                  v-model="datatablesParameters.search.value"
                  prefix-icon="el-icon-search"
                  :clearable="true"
                  @change="onAutoSearchChanged">
        </el-input>
        <cell-actions size="medium" type="button" :actions="toolActions"></cell-actions>
      </el-col>
    </el-row>
    <advance-search v-if="enableAdvanceSearch" :auto-search="true" :columns="filters"
                    @filter-search="onAdvanceFilterSearch"
                    @filter-remove="onAdvanceFilterRemove"></advance-search>
    <el-table
            :data="tableData"
            :default-sort="defaultSort"
            @sort-change="onSortChange"
            @selection-change="onSelectionChange"
            border
            size="mini"
            style="width: 100%"
            height="100vh"
            ref="table">
      <el-table-column
              v-if="enableSelection"
              fixed
              type="selection"
              width="55">
      </el-table-column>
      <slot></slot>
      <el-table-column
              v-if="actions"
              :width="actionsColumnWidth"
              fixed="right"
              :label="$t('global.terms.actions')">
        <template slot-scope="scope">
          <cell-actions :target="scope.row" :actions="actions"></cell-actions>
        </template>
      </el-table-column>
    </el-table>

    <div class="pagination-container">
          <span class="page-size">{{$t('global.terms.page_size_show')}}
          <el-select v-model="pagination.pageSize" style="width: 80px"
                     size="mini"
                     @change="onPageSizeChange">
            <el-option
                    v-for="item in pagination.pageSizeList"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
            </el-option>
          </el-select>
            {{$t('global.terms.page_size_items')}}</span>
      <el-pagination
              @current-change="onPageChange"
              :current-page="pagination.currentPage"
              :page-size="pagination.pageSize==-1?1:pagination.pageSize"
              :layout="pagination.pageSize==-1?'total':'total, prev, pager, next, jumper'"
              :total="pagination.totalCount">
      </el-pagination>
    </div>
  </div>
</template>

<script type="javascript">
  import {
    updateParams,
    removeSearchParams,
    hasPrefix,
    PARAMS_PAGE_SIZE,
    PARAMS_PAGE,
    PARAMS_SEARCH_TERM,
    PARAMS_SEARCH_PREFIX,
    PARAMS_SORT_COLUMN,
    PARAMS_SORT_DIRECTION
  } from '@/utils/addressbar';
  import { UnifiedValue, SerializerDatatablesParameters } from '@/utils/datatables';
  import _ from 'lodash'
  import downloadjs from 'downloadjs'
  import CellActions from '@/components/CellActions'
  import AdvanceSearch from '@/components/AdvanceSearch'

  export default {
    name: 'zgldh-datatables',
    components: {
      CellActions,
      AdvanceSearch
    },
    props: {
      source: {
        type: [Array, Function],
        required: true
      },
      title: {
        type: String,
        required: false
      },
      autoLoad: {
        type: Boolean,
        default: true
      },
      columnSelection: { // Show the column selection button
        type: Boolean,
        default: false
      },
      enableSelection: {
        type: Boolean,
        default: true
      },
      enableAddressBar: {
        type: Boolean,
        default: true
      },
      /**
       * An array like this:
       *   [
       {
         Title: () => this.$i18n.t('global.terms.download'),
         Handle: this.handleDownload
       },
       {
         Title: () => this.$i18n.t('global.terms.edit'),
         Handle: this.handleEdit
       },
       {
         Title: () => this.$i18n.t('global.terms.delete'),
         Handle: this.handleDelete
       },
       150  // Optional, the actions column width in px, or '10em' in custom width.
       ]
       */
      actions: {
        type: Array,
        default(){
          return [];
        }
      },
      multipleActions: {
        type: Array,
        default(){
          return [];
        }
      },
      filters: {
        type: Array,
        default(){
          return [];
        }
      },
      /**
       * Something like this
       {
         "name": this.$t('upload.fields.name'),
         "description": this.$t('upload.fields.description'),
         "disk": this.$t('upload.fields.disk'),
         "path": this.$t('upload.fields.path'),
         "size": this.$t('upload.fields.size'),
         "type": this.$t('upload.fields.type'),
         "created_at": this.$t('global.fields.created_at'),
       }
       **/
      exportColumns: {
        type: Object,
        required: false
      }
    },
    data(){
      return {
        loading: false,
        enableAdvanceSearch: hasPrefix(PARAMS_SEARCH_PREFIX),
        tableData: [],
        pagination: {
          currentPage: parseInt(this.$route.query[PARAMS_PAGE] || 1),
          totalCount: 0,
          pageSize: parseInt(this.$route.query[PARAMS_PAGE_SIZE] || 10),
          pageSizeList: [{
            label: 10,
            value: 10
          }, {
            label: 25,
            value: 25
          }, {
            label: 50,
            value: 50
          }, {
            label: 100,
            value: 100
          }, {
            label: this.$i18n.t('global.terms.page_size_all'),
            value: -1
          }],
        },
        datatablesParameters: {
          draw: 0,
          columns: [],
          order: [],
          start: 0,
          length: 10,
          search: {
            value: null,
            regex: false
          },
          _: null,
        },
        selectedItems: [],
        searchForm: {},
        defaultSort: {
          prop: this.$route.query[PARAMS_SORT_COLUMN] || 'created_at',
          order: this.$route.query[PARAMS_SORT_DIRECTION] || 'descending'
        },
        _draw: null,
      }
    },
    computed: {
      actionsColumnWidth(){
        var width;
        if (this.actions && this.actions.length) {
          var accessibleTypes = [Number, String];
          var items = [this.actions[0], this.actions[this.actions.length - 1]];
          width = items.find(item => accessibleTypes.includes(item.constructor));
        }
        width = width || 'auto';
        return width;
      },
      toolActions(){
        var vm = this;
        var defaultActions = [];
        defaultActions.push({
          Icon: 'fa-filter',
          Tooltip: () => this.$i18n.t('list.advance_search'),
          Handle(targets){
            vm.enableAdvanceSearch = !vm.enableAdvanceSearch;
          }
        });
        if (this.columnSelection) {
          defaultActions.push({
            Icon: 'fa-columns',
            Handle(targets){
              console.log('columns_setting', targets);
            }
          });
        }
        defaultActions.push({
          Icon: 'fa-download',
          Tooltip: () => this.$i18n.t('list.export_button'),
          Handle(targets){
            if (vm.source.constructor === Function) {
              var oldColumns = JSON.stringify(vm.datatablesParameters.columns);
              if (vm.exportColumns) {
                vm.datatablesParameters.columns = [];
                _.forEach(vm.exportColumns, (label, name) => {
                  vm.datatablesParameters.columns.push({
                    data: name,
                    name: name,
                    label: label
                  });
                });
              }
              else {
                vm.datatablesParameters.columns.map(column => {
                  var child = vm.$refs.table.$children.find(child => child.prop === column.name);
                  if (child) {
                    column.label = child.label;
                  }
                });
              }
              var parameters = vm.getQueryParameters();
              var fileName = vm.getExportFileName();
              parameters = parameters + '&_export=' + encodeURIComponent(fileName);
              vm.source(parameters).then(result => {
                downloadjs(result, fileName, 'text/csv');
              });
              vm.datatablesParameters.columns = JSON.parse(oldColumns);
            }
            else {
              console.info("Not support source type: ", typeof(source));
            }
          }
        });
        return defaultActions;
      }
    },
    mounted(){
      this.initializeDataTablesParameters();
      if (this.autoLoad) {
        this.loadSource();
      }
    },
    watch: {
      source(newSource, oldSource){
        if (newSource != oldSource) {
          return this.loadSource();
        }
      }
    },
    methods: {
      initializeDataTablesParameters: function () {
        // Consider this is initialize hook
        // if (typeof this.initializeSearchFormDataTablesParameters === 'function') {
        //   this.initializeSearchFormDataTablesParameters();
        // }
        this.initColumns();
        this.initAdvanceSearch();
        this.initPagination();

        this.datatablesParameters.order.push({
          column: this.defaultSort.prop,
          dir: this.defaultSort.order === 'descending' ? 'desc' : 'asc'
        });
      },
      initColumns(){
        let hasCreatedAt = false;
        if (this.$refs.table) {
          this.$refs.table.$children.forEach((column) => {
            if (column.$options._componentTag !== 'el-table-column') {
              return
            }
            if (!column.columnConfig.property) {
              return
            }
            if (!hasCreatedAt && column.columnConfig.property === 'created_at') {
              hasCreatedAt = true
            }
            this.datatablesParameters.columns.push({
              data: column.columnConfig.property,
              name: column.columnConfig.property,
              searchable: column.$el.getAttribute('searchable') ? (column.$el.getAttribute('searchable').toLowerCase() === 'true') : true,
              orderable: column.columnConfig.sortable == 'custom' ? true : false,
              search: {
                value: null,
                regex: false,
                advance: {}
              },
            })
          });
        } else {
          console.error('Thers is no table referenced in this component: ', this.$options._componentTag);
        }
        if (!hasCreatedAt) {
          this.datatablesParameters.columns.push({
            data: 'created_at',
            name: 'created_at',
            searchable: true,
            orderable: true,
            search: {
              value: null,
              regex: false,
              advance: {}
            },
          })
        }
      },
      initAdvanceSearch(){
        this.filters.forEach(filter => {
          var fieldName = filter.Field;
          if (!FindColumnConfigByName(this.datatablesParameters.columns, fieldName)) {
            this.datatablesParameters.columns.push({
              data: fieldName,
              name: fieldName,
              searchable: true,
              orderable: false,
              search: {
                value: null,
                regex: false,
                advance: {}
              },
            })
          }
        });
      },
      initPagination(){
        this.datatablesParameters.search.value = this.$route.query[PARAMS_SEARCH_TERM] || null;
        this.datatablesParameters.length = this.pagination.pageSize;
        this.datatablesParameters.start = (this.pagination.currentPage - 1) * this.pagination.pageSize;
      },
      loadSource: _.debounce(function () {
        if (this.loading) {
          return Promise.reject('loading');
        }
        switch (this.source.constructor) {
          case Array:
            this.tableData = this.source;
            Promise.resolve(this.source);
            break;
          case Function:
            this.loading = true;
            return this.source(this.getQueryParameters()).then(result => {
              if (this._draw == result.draw) {
                this.tableData = result.data;
                this.pagination.totalCount = result.recordsFiltered
              }
              this.loading = false;
              return result;
            }).catch(err => {
              this.loading = false;
            });
            break;
        }
      }, 500),
      getQueryParameters(){
        this.datatablesParameters.draw++
        this._draw = this.datatablesParameters.draw
        this.datatablesParameters._ = new Date().getTime()

        return SerializerDatatablesParameters(this.datatablesParameters);
      },
      onAutoSearchChanged (newValue) {
        this.loadSource()
        this.updateAddressBarParams(PARAMS_SEARCH_TERM, this.datatablesParameters.search.value)
      },
      onPageSizeChange: function () {
        this.datatablesParameters.length = this.pagination.pageSize
        this.loadSource()
        this.updateAddressBarParams(PARAMS_PAGE_SIZE, this.pagination.pageSize)
      },
      onPageChange: function (page) {
        this.pagination.currentPage = page
        this.datatablesParameters.start = (page - 1) * this.pagination.pageSize
        this.loadSource()
        this.updateAddressBarParams(PARAMS_PAGE, this.pagination.currentPage)
      },
      onSortChange: function ({
                                column,
                                prop,
                                order
                              }) {
        var newOrder = [];
        if (order !== null) {
          newOrder = [{
            'column': prop,
            'dir': (order === 'ascending' ? 'asc' : 'desc')
          }]
        }
        if (!_.eq(this.datatablesParameters.order, newOrder)) {
          this.datatablesParameters.order = newOrder;
          this.loadSource()
        }

        if (this.defaultSort.prop === prop && this.defaultSort.order === order) {
          removeSearchParams(PARAMS_SORT_COLUMN)
          removeSearchParams(PARAMS_SORT_DIRECTION);
        }
        else {
          this.updateAddressBarParams(PARAMS_SORT_COLUMN, prop)
          this.updateAddressBarParams(PARAMS_SORT_DIRECTION, order);
        }
      },
      onSelectionChange: function (selection) {
        this.selectedItems = selection
      },
      updateAddressBarParams: function () {
        if (this.enableAddressBar) {
          updateParams.apply(null, arguments);
        }
      },
      applyAdvanceSearchToColumn: function (columnName, operator, value) {
        var column = FindColumnConfigByName(this.datatablesParameters.columns, columnName);
        if (!column) {
          return;
        }
        column.search.advance[operator] = value
      },
      clearAdvanceSearchToColumn: function (columnName, operator) {
        var column = FindColumnConfigByName(this.datatablesParameters.columns, columnName)
        if (!column) {
          return;
        }
        if (operator) {
          delete column.search.advance[operator]
        } else {
          column.search.advance = {}
        }
      },
      onAdvanceFilterSearch(searches){
        if (searches.constructor !== Array) {
          searches = [searches];
        }
        searches.forEach(({ fieldName, operator, value }) => {
          this.applyAdvanceSearchToColumn(fieldName, operator, value);
        })
        this.loadSource();
      },
      onAdvanceFilterRemove({ fieldName, operator }){
        this.clearAdvanceSearchToColumn(fieldName, operator);
        this.loadSource();
      },
      getExportFileName(){
        var d = new Date();
        return this.$t('list.export_file_name', {
                  title: this.title,
                  timestamp: d.getTime()
                }) + '.csv';
      },
      // Exported functions
      removeItem(inputItem){
        var id = inputItem;
        if (inputItem.constructor === Object) {
          id = inputItem.id
        }
        var index = this.tableData.findIndex(item => item.id === id);
        if (index >= 0) {
          this.tableData.splice(index, 1);
          this.pagination.totalCount--;
        }
      },
      addRowMessage(id, message, type){
        var index = this.tableData.findIndex(item => item.id === id) + 1;
        var node = document.createElement('div');
        node.innerHTML = `<div role="alert" class="row-message-alert el-alert el-alert--${type}" onclick="this.remove()">
        <i class="el-alert__icon el-icon-${type}"></i>
        <div class="el-alert__content"><span class="el-alert__title">${message}</span></div></div>`;
        node = node.children[0];
        var row = this.$refs.table.$el.querySelector('tr.el-table__row:nth-child(' + index + ')');
        if (!row) {
          throw new Error(`Id ${id} can not be found.`);
        }
        row.appendChild(node);
      }
    }
  }

  function FindColumnConfigByName(columns, columnName) {
    return columns.find(column => {
      return column.name === columnName
    })
  }
</script>

<style lang="scss">
  @import "../../styles/variables.scss";

  .zgldh-datatables {
    .tools {
      margin-top: 6px;
      .cell-actions {
        display: inline-block;
      }
      .auto-search {
        vertical-align: middle;
        width: 200px;
        > input {
          height: 33px;
        }
      }
      .right-tools {
        text-align: right;
        .cell-actions {
          margin-left: 6px;
        }
      }
    }
    .advance-search {
      margin-top: 6px;
      text-align: right;
    }
    .el-table {
      margin-top: 6px;
    }
    .pagination-container {
      text-align: right;
      margin-top: 6px;
      .page-size {
        padding: 2px;
        vertical-align: top;
        color: $textPrimary;
        font-size: 13px;
        display: inline-block;
      }
      .el-pagination {
        display: inline-block;
      }
    }
    .row-message-alert {
      position: absolute;
      left: 0;
      width: auto;
      margin: 0.5em;
      z-index: 10;
      cursor: pointer;
    }
  }
</style>
