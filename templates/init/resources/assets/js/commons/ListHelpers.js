import {Loading} from 'element-ui';
import _ from 'lodash';

export var mixin = {
  data: function () {
    return {
      pagination: {
        currentPage: 1,
        totalCount: 0,
        pageSize: 25,
        pageSizeList: PAGE_SIZE_LIST,
      },

      selectedItems: [],

      searchForm: {},

      tableData: [],
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
      loading: null,
      _draw: null,
    };
  },
  mounted: function () {
    this.initializeDataTablesParameters();
    this.queryTableData();
  },
  methods: {
    showLoading: function (target) {
      this.loading = Loading.service({
        target: target,
        fullscreen: false,
        lock: false,
        text: '读取中...'
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
    onSubmitSearch: function () {
      this.buildSearchParameters();
      this.queryTableData();
    },
    onCreate: function () {
      alert('onCreate');
    },
    onBundleDelete: function () {
      alert('onBundleDelete');
    },
    onSortChange: function ({column, prop, order}) {
      this.datatablesParameters.order = [{'column': prop, 'dir': (order == 'ascending' ? 'asc' : 'desc')}];
      this.queryTableData();
    },
    handleClick: function () {
      alert('handleClick');
    },
    handleSizeChange: function () {
      this.datatablesParameters.length = this.pagination.pageSize;
      this.queryTableData();
    },
    handlePageChange: function (page) {
      this.pagination.currentPage = page;
      this.datatablesParameters.start = (page - 1) * this.pagination.pageSize;
      this.queryTableData();
    },
    onAutoSearchChanged: _.debounce(function (newValue) {
      this.queryTableData();
    }, 500),
    onAutoSearchIconClick: function (newValue) {
      if (this.datatablesParameters.search.value) {
        this.datatablesParameters.search.value = null;
        this.queryTableData();
      }
    },
    initializeDataTablesParameters: function () {
      this.$refs.table.$children.forEach((column, index, columns) => {
        if (column.$options._componentTag !== "el-table-column") {
          return;
        }
        if (!column.columnConfig.property) {
          return;
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
        var value = item.value;
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
    'el-input', 'el-select', 'el-date-picker'
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

export const PAGE_SIZE_LIST = [
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
    label: '全部',
    value: -1
  }
];