import { Loading } from 'element-ui';
import _ from 'lodash';

const PARAMS_PAGE_SIZE = 'pageSize';
const PARAMS_PAGE = 'page';
const PARAMS_SEARCH_TERM = 'term';
const PARAMS_SEARCH_PREFIX = 's--';
const PARAMS_SORT_COLUMN = 'sort';
const PARAMS_SORT_DIRECTION = 'dir';

export var mixin = {
  data: function () {
    return {
      pagination: {
        currentPage: 1,
        totalCount: 0,
        pageSize: 25,
        pageSizeList: [
          {
            label: 25,
            value: 25
          },
          {
            label: 50,
            value: 50
          },
          {
            label: 100,
            value: 100
          },
          {
            label: this.$i18n.t('scaffold.terms.page_size_all'),
            value: -1
          }
        ],
        $enableAddressBar: true    // true: 在浏览器地址栏保存 currentPage 和 pageSize
      },

      searchForm: {},

      tableData: [],
      selectedItems: [],
      datatablesParameters: {
        draw: 0,
        columns: [],
        order: [],
        start: 0,
        length: 25,
        search: {
          value: null,
          regex: false
        },
        _: null,
      },

      defaultSort: {prop: 'date', order: 'descending'},

      loading: null,
      _draw: null,
    };
  },
  beforeMount: function () {
    if (this.pagination.$enableAddressBar) {
      this.pagination.currentPage = this.$route.query.hasOwnProperty(PARAMS_PAGE) ? parseInt(this.$route.query[PARAMS_PAGE]) : 1;
      this.pagination.pageSize = this.$route.query.hasOwnProperty(PARAMS_PAGE_SIZE) ? parseInt(this.$route.query[PARAMS_PAGE_SIZE]) : 25;
      if (this.$route.query.hasOwnProperty(PARAMS_SORT_COLUMN)) {
        this.defaultSort = {
          prop: this.$route.query[PARAMS_SORT_COLUMN],
          order: this.$route.query[PARAMS_SORT_DIRECTION]
        };
      }
    }
  },
  mounted: function () {
    this.initializeDataTablesParameters();
    if (!this.$route.query.hasOwnProperty(PARAMS_SORT_COLUMN)) {
      this.queryTableData();
    }
  },
  methods: {
    initializeDataTablesParameters: function () {
      let hasCreatedAt = false;
      this.$refs.table.$children.forEach((column, index, columns) => {
        if (column.$options._componentTag !== "el-table-column") {
          return;
        }
        if (!column.columnConfig.property) {
          return;
        }
        if (!hasCreatedAt && column.columnConfig.property === 'created_at') {
          hasCreatedAt = true;
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
        });
      });

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
        });
      }

      if (this.pagination.$enableAddressBar) {
        let hasSearchParams = false;
        _.forEach(this.$route.query, (value, key) => {
          if (key.indexOf(PARAMS_SEARCH_PREFIX) !== -1) {
            this.searchForm[key.substr(PARAMS_SEARCH_PREFIX.length)] = value;
            hasSearchParams = true;
          }
        });
        if (hasSearchParams) {
          this.buildSearchParameters();
        }
        this.datatablesParameters.search.value = this.$route.query.hasOwnProperty(PARAMS_SEARCH_TERM) ? this.$route.query[PARAMS_SEARCH_TERM] : null;
      }
      this.datatablesParameters.length = this.pagination.pageSize;
      this.datatablesParameters.start = (this.pagination.currentPage - 1) * this.pagination.pageSize;
    },
    showLoading: function (target) {
      this.loading = Loading.service({
        target: target,
        fullscreen: false,
        lock: false,
        text: this.$i18n.t('scaffold.terms.loading') + '...'
      });
    },
    hideLoading: function () {
      this.loading.close();
    },
    queryTableData: function () {
      if (this.loading && this.loading.visible) {
        return false;
      }
      this.showLoading('.datatable-loading-section');
      axios.get(this.resource, {
        params: this.buildDataTablesParameters(),
        paramsSerializer: SerializerDatatablesParameters
      }).then(result => {
        if (this._draw == result.data.draw) {
          this.tableData = result.data.data;
          this.pagination.totalCount = result.data.recordsFiltered;
          this.hideLoading();
        }
      }).catch(result => {
        this.hideLoading();
      });
    },
    updateAddressBarParams: function () {
      if (this.pagination.$enableAddressBar) {
        let payload = JSON.parse(JSON.stringify(this.$route.query));
        let queries = {};
        if (arguments.length === 2) {
          queries[arguments[0]] = arguments[1];
        }
        else {
          queries = arguments[0];
        }
        _.forEach(queries, function (data, key) {
          if (data === null) {
            delete payload[key];
          }
          else {
            payload[key] = data;
          }
        });
        this.$router.push({
          path: this.$route.path,
          query: payload
        });
      }
    },
    clearSearchFormAddressBarParams: function (searchPrefix) {
      let payload = JSON.parse(JSON.stringify(this.$route.query));
      _.forEach(payload, function (data, key) {
        if (key.indexOf(searchPrefix) !== -1) {
          delete payload[key];
        }
      });
      this.$router.push({
        path: this.$route.path,
        query: payload
      });
    },
    onPageSizeChange: function () {
      this.datatablesParameters.length = this.pagination.pageSize;
      this.queryTableData();
      this.updateAddressBarParams(PARAMS_PAGE_SIZE, this.pagination.pageSize);
    },
    onPageChange: function (page) {
      this.pagination.currentPage = page;
      this.datatablesParameters.start = (page - 1) * this.pagination.pageSize;
      this.queryTableData();
      this.updateAddressBarParams(PARAMS_PAGE, this.pagination.currentPage);
    },
    onSortChange: function ({column, prop, order}) {
      if (order === null) {
        this.datatablesParameters.order = [];
      }
      else {
        this.datatablesParameters.order = [{'column': prop, 'dir': (order == 'ascending' ? 'asc' : 'desc')}];
      }
      this.queryTableData();
      this.updateAddressBarParams({
        'sort': prop,
        'dir': order
      });
    },
    onAutoSearchChanged: _.debounce(function (newValue) {
      this.queryTableData();
      this.updateAddressBarParams(PARAMS_SEARCH_TERM, this.datatablesParameters.search.value);
    }, 500),
    onAutoSearchIconClick: function (newValue) {
      if (this.datatablesParameters.search.value) {
        this.datatablesParameters.search.value = null;
        this.queryTableData();
        this.updateAddressBarParams(PARAMS_SEARCH_TERM, null);
      }
    },
    onSubmitSearch: function () {
      this.buildSearchParameters();
      this.queryTableData();
      this.updateAddressBarParams(PrefixObject(this.searchForm, PARAMS_SEARCH_PREFIX));
    },
    onResetSearch: function () {
      this.searchForm = {};
      this.$nextTick(this.onSubmitSearch);
      this.clearSearchFormAddressBarParams(PARAMS_SEARCH_PREFIX);
    },
    onSelectionChange: function (selection) {
      this.selectedItems = selection;
    },
    _onDeleteClick: function ({url, data, confirmText, messageText}) {
      data = data ? data : {};
      data._method = 'delete';
      return this.$confirm(confirmText, this.$i18n.t('scaffold.terms.alert'), {
        confirmButtonText: this.$i18n.t('scaffold.terms.confirm'),
        cancelButtonText: this.$i18n.t('scaffold.terms.cancel'),
        type: 'warning'
      }).then(() => {
        return axios.post(url, data)
      }).then(result => {
        this.$message({
          type: 'success',
          message: messageText
        });
        return result.data;
      }, ({response}) => {
        this.$message({
          type: 'error',
          message: response.data.message
        });
      });
    },

    _onBundle: function (action, resourceUrl, options, items) {
      var selectedItems = JSON.parse(JSON.stringify(items ? items : this.selectedItems));
      return axios.post(resourceUrl ? resourceUrl : ( this.resource + '/bundle'), {
        action: action,
        indexes: selectedItems.map((item) => item.id),
        options: options
      }).then(response => {
        if (response && response.status == 200) {
          return response;
        }
        else {
          throw new Error(response.data);
        }
      }).catch(({response}) => {
        this.$message({
          type: 'error',
          message: response.data.message
        });
        throw response;
      });
    },
    buildDataTablesParameters: function () {
      this.datatablesParameters.draw++;
      this._draw = this.datatablesParameters.draw;
      this.datatablesParameters._ = new Date().getTime();
      return this.datatablesParameters;
    },
    buildSearchParameters: function () {
      var searchComponents = FindSearchComponents(this.$refs.searchForm);
      searchComponents.forEach(item => {
        // console.log('search',item.);
        var columnName = item.$el.getAttribute('column');
        var operator = item.$el.getAttribute('operator') ? item.$el.getAttribute('operator') : '=';
        var value = this.searchForm[columnName];
        if (value === '' || value === null || value === undefined) {
          this.clearAdvanceSearchToColumn(columnName, operator);
        }
        else {
          switch (operator) {
            case 'range':
              if (value[0]) {
                this.applyAdvanceSearchToColumn(columnName, '>=', UnifiedValue(value[0]));
              }
              else {
                this.clearAdvanceSearchToColumn(columnName, '>=');
              }
              if (value[1]) {
                this.applyAdvanceSearchToColumn(columnName, '<=', UnifiedValue(value[1], true));
              }
              else {
                this.clearAdvanceSearchToColumn(columnName, '<=');
              }
              break;
            case 'like':
              this.applyAdvanceSearchToColumn(columnName, operator, '%' + UnifiedValue(value) + '%');
              break;
            default:
              // >, >=, =, <=, <
              this.applyAdvanceSearchToColumn(columnName, operator, UnifiedValue(value));
              break;
          }
        }
      });
    },
    applyAdvanceSearchToColumn: function (columnName, operator, value) {
      var column = FindColumnConfigByName(this.datatablesParameters.columns, columnName);
      column.search.advance[operator] = value;
    },
    clearAdvanceSearchToColumn: function (columnName, operator) {
      var column = FindColumnConfigByName(this.datatablesParameters.columns, columnName);
      if (operator) {
        delete column.search.advance[operator];
      }
      else {
        column.search.advance = {};
      }
    }
  }
};

function UnifiedValue (value, dayEnd) {
  var result = value;
  if (_.isDate(value)) {
    result = value.getFullYear() + '-' + (value.getMonth() + 1) + '-' + value.getDate();
    if (dayEnd) {
      result += ' 23:59:59';
    }
    else {
      result += ' ' + value.getHours() + ':' + value.getMinutes() + ':' + value.getSeconds();
    }
  }
  return result;
}

function FindSearchComponents (inputComponent) {
  const CONTROLLERS = [
    'el-input', 'el-select', 'el-date-picker', 'el-time-picker'
  ];
  var components = [];
  inputComponent.$children.forEach(component => {
    if (component.$el.hasAttribute('manual-search')) {
      // 当一个控件含有 manual-search 属性时， 不自动处理参数。 需要在 onSubmitSearch 函数中手工处理
      // Ignore.
    }
    else if (CONTROLLERS.indexOf(component.$options._componentTag) !== -1) {
      components.push(component);
    }
    else if (component.hasOwnProperty('$children')) {
      [].push.apply(components, FindSearchComponents(component));
    }
    else {
      // Ignore
    }
  });
  return components;
}

function FindColumnConfigByName (columns, columnName) {
  var column = columns.find(column => {
    return column.name === columnName;
  });
  if (!column) {
    throw new Error("Can't find column named '" + columnName + "'");
  }
  return column;
}

function PrefixObject (params, prefix) {
  var result = {};
  _.forEach(params, function (val, key) {
    result[prefix + key] = val;
  });
  return result;
}

function SerializerDatatablesParameters (params) {
  var parameters = JSON.parse(JSON.stringify(params));
  delete parameters.order;
  delete parameters.columns;
  delete parameters.search;
  params.columns.forEach((element, index) => {
    if (!element.data) {
      return;
    }
    parameters['columns[' + index + '][data]'] = element.data;
    parameters['columns[' + index + '][name]'] = element.name;
    parameters['columns[' + index + '][searchable]'] = element.searchable;
    parameters['columns[' + index + '][orderable]'] = element.orderable;
    parameters['columns[' + index + '][search][value]'] = element.search.value ? element.search.value : null;
    parameters['columns[' + index + '][search][regex]'] = element.search.regex ? element.search.regex : false;
    if (element.search.hasOwnProperty('advance')) {
      for (var operator in element.search.advance) {
        if (element.search.advance.hasOwnProperty(operator)) {
          parameters['columns[' + index + '][search][advance][' + operator + ']'] = element.search.advance[operator];
        }
      }
    }
  });
  params.order.forEach(function (element, index) {
    parameters['order[' + index + '][column]'] = isNaN(element.column) ? params.columns.findIndex(column => {
      return column.data === element.column;
    }) : parseInt(element.column);
    parameters['order[' + index + '][dir]'] = element.dir ? element.dir : 'asc';
  });
  parameters['search[value]'] = params.search.value !== undefined ? params.search.value : null;
  parameters['search[regex]'] = params.search.regex ? params.search.regex : false;
  var parameterString = [];
  for (var key in parameters) {
    if (parameters.hasOwnProperty(key)) {
      if ([undefined, null, ''].indexOf(parameters[key]) === -1) {
        parameterString.push(encodeURIComponent(key) + '=' + encodeURIComponent(parameters[key]));
      }
      else {
        parameterString.push(encodeURIComponent(key) + '=');
      }
    }
  }
  parameterString = parameterString.join('&');
  return parameterString;
}
