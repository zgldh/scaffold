export var mixin = {
  data: function () {
    return {
      currentPage: 1,
      pageSize: 25,
      pageSizeList: pageSizeList,

      selectedItems: [],

      searchTerm: null,
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
      loading: true
    };
  },
  mounted: function () {
    this.initializeDataTablesParameters();
    this.queryTableData();
  },
  methods: {
    queryTableData: function () {
      axios.get(this.resource, {
        params: this.buildDataTablesParameters(),
        paramsSerializer: (params) => {
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
            parameters['columns[' + index + '][searchable]'] = element.searchable ? element.searchable : true;
            parameters['columns[' + index + '][orderable]'] = element.orderable ? element.orderable : true;
            parameters['columns[' + index + '][search][value]'] = element.search.value ? element.search.value : null;
            parameters['columns[' + index + '][search][regex]'] = element.search.regex ? element.search.regex : false;
            if (element.search.hasOwnProperty('advance')) {
              for (var operator in element.search.advance) {
                if (element.search.advance.hasOwnProperty(operator)) {
                  parameters['columns[' + index + '][search][advance][' + operator + ']'] = val;
                }
              }
            }
          });
          params.order.forEach(function (element, index) {
            parameters['order[' + index + '][column]'] = element.column;
            parameters['order[' + index + '][dir]'] = element.dir ? element.dir : 'asc';
          });
          parameters['search[value]'] = params.search.value !== undefined ? params.search.value : null;
          parameters['search[regex]'] = params.search.regex ? params.search.regex : false;
          var parameterString = [];
          for (var key in parameters) {
            if (parameters.hasOwnProperty(key)) {
              if ([undefined, null, ''].indexOf(parameters[key]) === -1) {
                parameterString.push(key + '=' + parameters[key]);
              }
              else {
                parameterString.push(key + '=');
              }
            }
          }
          parameterString = encodeURI(parameterString.join('&'));
          return parameterString;
        }
      })
        .then(result => {
          this.tableData = result.data.data;
          this.loading = false;
        });
    },
    onSubmitSearch: function () {
      alert('onSubmitSearch');
    },
    onCreate: function () {
      alert('onCreate');
    },
    onBundleDelete: function () {
      alert('onBundleDelete');
    },
    handleClick: function () {
      alert('handleClick');
    },
    handleSizeChange: function () {
      alert('handleSizeChange');
    },
    handleCurrentChange: function () {
      alert('handleCurrentChange');
    },
    initializeDataTablesParameters: function () {
      this.$refs.table.columns.forEach((column, index, columns) => {
        if (!column.property) {
          return;
        }
        this.datatablesParameters.columns.push({
          data: column.property,
          name: column.property,
          searchable: true,
          orderable: column.sortable,
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
      this.datatablesParameters._ = new Date().getTime();
      return this.datatablesParameters;
    }
  }
};


export function loadTableData (resource) {

}

export function onDelete () {

}
export function onBundle () {

}

export var pageSizeList = [
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