/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../bootstrap');
require('../custom');
require('../../vendor/admin-lte/js/app');

// Add a response interceptor
window.axios.interceptors.response.use(function (response) {
  // Do something with response data
  return response;
}, function (error) {
  // Do something with response error
  if (error.response.status == 401) {
    // Jump to /admin/login page
    alert('您未登录。');
    window.location.href = '/admin/login?redirect=' + encodeURIComponent(window.location.href);
  }
  return Promise.reject(error);
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import Vue from 'vue';
import VueI18n from 'vue-i18n';
import VueRouter from 'vue-router';
import enLocale from 'element-ui/lib/locale/lang/en';
import zhLocale from 'element-ui/lib/locale/lang/zh-CN';

Vue.use(VueRouter);
Vue.use(VueI18n);
Vue.use(require('element-ui'), {
  i18n: function (path, options) {
    // ...
    console.log('i18n', path, options);
    return i18n.t(path, null, options);
  }
});

const i18n = new VueI18n({
  locale: window.Laravel.Locale, // set locale
});
i18n.mergeLocaleMessage(window.Laravel.Locale, window.Laravel.Languages);
i18n.mergeLocaleMessage('zh-CN', zhLocale);
i18n.mergeLocaleMessage('en', enLocale);

Vue.component('RouterTreeview', require('../components/RouterTreeview.vue'));

var routes = [].concat(
// Modules routes
);

const router = new VueRouter({
  routes, // （缩写）相当于 routes: routes,
  linkActiveClass: 'active'
});

const app = new Vue({
  i18n: i18n,
  router: router,
  store: require('../commons/store')
}).$mount('#app');
