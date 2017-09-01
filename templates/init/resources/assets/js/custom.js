// Add a request interceptor
window.axios.interceptors.request.use(function (config) {
  // Do something before request is sent
  return config;
}, function (error) {
  // Do something with request error
  return Promise.reject(error);
});

// Add a response interceptor
window.axios.interceptors.response.use(function (response) {
  // Do something with response data
  return response;
}, function (error) {
  // Do something with response error
  return Promise.reject(error);
});

import "materialize-css";
import "babel-polyfill";

import Vue from 'vue';
import VueI18n from 'vue-i18n';
Vue.use(VueI18n);