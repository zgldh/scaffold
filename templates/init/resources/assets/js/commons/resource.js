import Vue from 'vue'
import VueResource from 'vue-resource'
import NProgress from 'nprogress'

NProgress.configure({showSpinner: false});

Vue.use(VueResource);
Vue.http.options.root = '';  // No tail slash
Vue.http.options.emulateHTTP = true;
Vue.http.options.emulateJSON = false;
Vue.http.interceptors.push((request, next) => {
  if (/https?:\/\//.test(request.url) === false) {
    var xsrfToken = getXsrfToken();
    if (xsrfToken) {
      request.headers.set('X-XSRF-TOKEN', xsrfToken);
    }

    var method = request.method.toLowerCase();
    if (method !== 'post' && method !== 'get') {
      if (request.body) {
        request.body._method = method;
      }
      else {
        request.body = {_method: method};
      }
      request.method = 'POST';
    }

    if (request.body && request.body.hasOwnProperty('$errors')) {
      delete request.body.$errors;
    }

    if (method == 'post' || method == 'put') {
      var formData = new FormData();
      var hasFile = false;
      for (var key in request.body) {
        var item = request.body[key];
        if (item && item.constructor == File) {
          hasFile = true;
          formData.append(key, item);
        }
        else {
          formData.append(key, JSON.stringify(item));
        }
      }

      if (hasFile) {
        if (method == 'put') {
          formData.append('_method', 'put');
        }
        request.body = formData;
      }
    }
  }
  // TODO 如果含有 file 类型， 则作相应调整。

  NProgress.start();

  // continue to next interceptor
  next((response) => {
    NProgress.done();
    return response;
  });
});

function getCookie (cookieName) {
  if (document.cookie.length > 0) {
    var cookieStart = document.cookie.indexOf(cookieName + '=');
    if (cookieStart !== -1) {
      cookieStart = cookieStart + cookieName.length + 1;
      var cookieEnd = document.cookie.indexOf(';', cookieStart);
      if (cookieEnd === -1) cookieEnd = document.cookie.length;
      return decodeURIComponent(document.cookie.substring(cookieStart, cookieEnd));
    }
  }
  return '';
}

function getXsrfToken () {
  return getCookie('XSRF-TOKEN');
}

import {alert} from 'resources/assets/js/components/SweetAlertDialogs';

function errorCallback (err) {
  if (err.status == 422) {
    var messages = [];
    for (var key in err.data) {
      if (err.data.hasOwnProperty(key)) {
        var obj = err.data[key];
        for (var prop in obj) {
          // important check that this is objects own property
          // not from prototype prop inherited
          if (obj.hasOwnProperty(prop)) {
            messages.push(obj[prop]);
          }
        }
      }
    }
    alert(messages.join("<br>"));
  }
  else if (err.status >= 400 && err.status < 500) {
    alert(err.data.message);
  }
}

export {getXsrfToken, errorCallback};
