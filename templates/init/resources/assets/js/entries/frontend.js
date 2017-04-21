require("../../scss/frontend.scss");

import Vue from 'vue'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-default/index.css'

window.Vue = Vue;
Vue.use(ElementUI);
Vue.component('reset-password', ResetPassword);
Vue.component('show-dialog', ShowDialog);
$('.switch_tab').click(function(){
    console.log(213);
});