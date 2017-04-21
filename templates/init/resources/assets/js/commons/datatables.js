// JS
import 'datatables.net';
import 'datatables.net-bs';
import 'datatables.net-buttons';
import 'datatables.net-buttons-bs';

$.fn.dataTable.ext.errMode = 'throw';
$.fn.dataTable.defaults.language = {
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

//
import 'datatables.net-buttons/js/buttons.colVis.js'; // Column visibility
import 'datatables.net-buttons/js/buttons.html5.js';  // HTML 5 file export
import 'datatables.net-buttons/js/buttons.flash.js';  // Flash file export
import 'datatables.net-buttons/js/buttons.print.js';  // Print view button

// Select
import 'datatables.net-select';
import 'datatables.net-select-dt/css/select.dataTables.css';

// Style
import 'datatables.net-bs/css/dataTables.bootstrap.css';
import 'datatables.net-buttons-bs/css/buttons.bootstrap.css';
