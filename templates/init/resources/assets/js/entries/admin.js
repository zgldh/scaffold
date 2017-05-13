/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../bootstrap');
require('../custom');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import Vue from 'vue';
import VueRouter from 'vue-router';
import ElementUI from 'element-ui';
Vue.use(VueRouter);
Vue.use(ElementUI);

Vue.component(
  'passport-clients',
  require('../components/passport/Clients.vue')
);

Vue.component(
  'passport-authorized-clients',
  require('../components/passport/AuthorizedClients.vue')
);

Vue.component(
  'passport-personal-access-tokens',
  require('../components/passport/PersonalAccessTokens.vue')
);

import Layout from '../admin/Layout.vue';

var routes = [
  {path: '/login', component: () => System.import('Modules/User/resources/assets/user/LoginPage.vue')},
  {
    path: '/', component: Layout,
    children: [].concat(
      require('Modules/User/resources/assets/routes.js').default,
    )
  }
];

const router = new VueRouter({
  routes // （缩写）相当于 routes: routes
});

const app = new window.Vue({
  router: router,
  components: {
    Layout
  },
  created: function () {
    axios.get('/api/user').then(function (result) {
      console.log('result', result);
    }, function (error) {
      console.log('error', error);
      if (error.response.status == 401) {
        app.$router.push('login');
      }
    })
  }
}).$mount('#app');
