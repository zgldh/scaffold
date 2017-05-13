const datatablesLanguage = {
  "sProcessing": "处理中...",
  "sLengthMenu": "显示 _MENU_ 项结果",
  "sZeroRecords": "没有匹配结果",
  "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
  "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
  "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
  "sInfoPostFix": "",
  "sSearch": "搜索:",
  "sUrl": "",
  "sEmptyTable": "表中数据为空",
  "sLoadingRecords": "载入中...",
  "sInfoThousands": ",",
  "oPaginate": {
    "sFirst": "首页",
    "sPrevious": "上页",
    "sNext": "下页",
    "sLast": "末页"
  },
  "oAria": {
    "sSortAscending": ": 以升序排列此列",
    "sSortDescending": ": 以降序排列此列"
  },
  "select": {
    "rows": {
      _: "选择了 %d 项",
      0: "点击复选框可以选择一项",
      1: "选择了 1 项"
    }
  }
};

const datatablesHelper = {
  /**
   * 生成 Datatables 配置对象
   * @param resourceURL {resource: resourceURL,with: ['province', 'city', 'district']}
   * @param columns
   * @returns {{processing: boolean, serverSide: boolean, ajax, rowId: string, columns: *, order: Array, language: {sProcessing: string, sLengthMenu: string, sZeroRecords: string, sInfo: string, sInfoEmpty: string, sInfoFiltered: string, sInfoPostFix: string, sSearch: string, sUrl: string, sEmptyTable: string, sLoadingRecords: string, sInfoThousands: string, oPaginate: {sFirst: string, sPrevious: string, sNext: string, sLast: string}, oAria: {sSortAscending: string, sSortDescending: string}}}}
   */
  buildDatatablesConfig: function (resourceURL, columns) {
    if (resourceURL.constructor == Object) {
      resourceURL = resourceURL.resource + "?_with=" + encodeURIComponent(resourceURL.with);
      resourceURL += resourceURL.with ? "?_with=" + encodeURIComponent(resourceURL.with) : '';
    }
    columns.splice(0, 0, {
      className: 'select-checkbox',
      searchable: false,
      sortable: false,
      render: function (data) {
        return '';
      }
    });
    columns.push({
      title: '操作',
      searchable: false,
      sortable: false,
      render: function (data, type, row) {
        return '<button class="btn btn-default btn-flat edit-item-btn" data-item-id="' + row.id + '"><i class="fa fa-edit"></i> 编辑</button>' +
          '<button class="btn btn-danger btn-flat delete-item-btn" data-item-id="' + row.id + '"><i class="fa fa-trash"></i> 删除</button>';
      }
    });
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
      order: [],
      language: datatablesLanguage,
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

export default datatablesHelper;