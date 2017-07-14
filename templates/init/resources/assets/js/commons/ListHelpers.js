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
      axios.get(this.resource, {params: this.buildDataTablesParameters()})
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
      var parameters = JSON.parse(JSON.stringify(this.datatablesParameters));
      return parameters;
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