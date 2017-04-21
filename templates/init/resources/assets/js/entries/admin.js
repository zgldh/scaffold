require('../commons/datatables.js');

require("resources/assets/vendor/admin-lte/css/AdminLTE.css");
require("admin-lte");
require("../../scss/admin.scss");

import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router';
Vue.use(Vuex);
Vue.use(VueRouter);

var routes = [].concat(
  require('$NAME$/Dashboard/resources/assets/routes.js').default,
  require('$NAME$/User/resources/assets/routes.js').default,
  require('$NAME$/ActionLog/resources/assets/routes.js').default,
  require('$NAME$/Upload/resources/assets/routes.js').default,
// Modules routes
);

const router = new VueRouter({
  routes, // （缩写）相当于 routes: routes,
  linkActiveClass: 'active'
});

import store from '../commons/store';

const app = new Vue({
  router: router,
  store: store,
  components: {},
  mounted: function () {
    console.log(this.$route);
  }
}).$mount('#app');
