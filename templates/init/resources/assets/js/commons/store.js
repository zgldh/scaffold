import Vuex from 'vuex';
import Vue from 'vue';
Vue.use(Vuex);

// Store functions
const store = new Vuex.Store({
  state: {
    selectedItems: [],
    currentPermissions: []
  },
  mutations: {
    setSelectedItems: function (state, items) {
      if (items && items.constructor === Number) {
        items = [{id: items}];
      }
      else if (items && items.constructor !== Array) {
        items = [items];
      }
      state.selectedItems = items;
    },
    setCurrentPermissions: function (state, permission) {
      state.currentPermissions = permission;
    }
  },
  actions: {}
});
export default store;
