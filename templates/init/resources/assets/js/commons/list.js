function load (callback) {
  require('bundle-loader!./vuejs.js')(function (vuejs) {
    let Vue = vuejs.Vue;
    let store = vuejs.store;
    let moduleEntryConfig = callback();
    var config = {
      el: '#app',
      data: {},
      store: store
    };
    if (moduleEntryConfig) {
      config = Object.assign(config, moduleEntryConfig.config);
    }
    let app = new Vue(config);
  });
}

export default function (callback) {
  load(callback);
};