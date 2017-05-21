// Style
require("../../scss/custom.scss");
require("bootstrap-sass/assets/stylesheets/_bootstrap.scss");
require("bootstrap-datepicker/dist/css/bootstrap-datepicker3.css");
require("icheck/skins/all.css");
require("font-awesome/scss/font-awesome.scss");
require("ionicons/dist/scss/ionicons.scss");
require("resources/assets/vendor/admin-lte/css/skins/_all-skins.css");
require('sweetalert2/dist/sweetalert2.min.css');
require('select2/dist/css/select2.css');
require("resources/assets/vendor/admin-lte/css/AdminLTE.css");
require("toastr/toastr.scss");

// JS
window.jQuery = window.$ = require("jquery");
require("bootstrap-sass");
require("bootstrap-datepicker");
require("bootstrap-datepicker/dist/locales/bootstrap-datepicker.zh-CN.min.js");
require("icheck");
require("admin-lte");
require('sweetalert2');
require('select2/dist/js/select2.full.js');
require('select2/dist/js/i18n/zh-CN.js');
window.toastr = require('toastr');
window.BigScreen = require('bigscreen');
//

$(function () {
  $('.icheck').iCheck({
    checkboxClass: 'icheckbox_square-blue',
    radioClass: 'iradio_square-blue',
    increaseArea: '20%'
  });
});