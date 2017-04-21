const datatablesHelper = {
  /**
   * 生成 Datatables 配置对象
   * @param resourceURL {resource: resourceURL,with: ['province', 'city', 'district']}
   * @param columns
   * @returns {{processing: boolean, serverSide: boolean, ajax, rowId: string, columns: *, order: Array, language: {sProcessing: string, sLengthMenu: string, sZeroRecords: string, sInfo: string, sInfoEmpty: string, sInfoFiltered: string, sInfoPostFix: string, sSearch: string, sUrl: string, sEmptyTable: string, sLoadingRecords: string, sInfoThousands: string, oPaginate: {sFirst: string, sPrevious: string, sNext: string, sLast: string}, oAria: {sSortAscending: string, sSortDescending: string}}}}
   */
  buildDatatablesConfig: function (inputResourceURL, columns) {
    var resourceURL = '';
    if (inputResourceURL.constructor == Object) {
      resourceURL = inputResourceURL.resource;
      resourceURL += inputResourceURL.with ? "?_with=" + encodeURIComponent(inputResourceURL.with) : '';
    }
    columns = setupColumns(columns, inputResourceURL);
    let config = {
      processing: true,
      serverSide: true,
      ajax: resourceURL,
      rowId: 'id',
      columns: columns,
      select: {
        style: 'multi',
        selector: 'td.select-checkbox'
      },
      pageLength: 25,
      order: [],
      initComplete: function (settings, json) {
        $(this).on('click', 'th.select-checkbox', function (e) {
          var th = $(this);
          var tr = th.parent('tr');
          var table = $(e.delegateTarget);
          if (tr.is('.selected')) {
            table.DataTable().rows().deselect();
            tr.removeClass('selected');
          }
          else {
            table.DataTable().rows().select();
            tr.addClass('selected');
          }
        });
      }
    };
    return config;
  }
};

function setupColumns (columns, inputResourceURL) {

  // setup checkbox
  columns.splice(0, 0, {
    className: 'select-checkbox',
    searchable: false,
    sortable: false,
    render: function (data) {
      return '';
    }
  });

  // get action column
  var defaultRender = function (data, type, row, meta) {
    return '<button class="btn btn-default btn-flat edit-item-btn" data-item-id="' + row.id + '"><i class="fa fa-edit"></i> 编辑</button>' +
      '<button class="btn btn-danger btn-flat delete-item-btn" data-item-id="' + row.id + '"><i class="fa fa-trash"></i> 删除</button>';
  };
  var actionColumn = {
    title: '操作',
    searchable: false,
    sortable: false
  };
  var noAction = true;
  for (var i = 0, len = columns.length; i < len; i++) {
    var column = columns[i];

    if (column.type !== 'action') {
      continue;
    }
    noAction = false;
    var tempColumn = $.extend({}, column);
    column = actionColumn;
    column.title = tempColumn.title ? tempColumn.title : column.title;
    if (tempColumn.additional) {
      column.render = function (data, type, row, meta) {
        return defaultRender(data, type, row, meta) + tempColumn.render(data, type, row, meta);
      };
    }
    else if (tempColumn.render) {
      column.render = tempColumn.render;
    }
    else {
      column.render = defaultRender;
    }

    columns[i] = column;
  }

  if (noAction) {
    columns.push($.extend({}, actionColumn, {render: defaultRender}));
  }

  return columns;
}

export default datatablesHelper;