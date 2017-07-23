import Vuex from 'vuex';
import Vue from 'vue';
Vue.use(Vuex);

// Store functions
const store = new Vuex.Store({
  state: {
    currentPermissions: []
  },
  mutations: {
    setCurrentPermissions: function (state, permission) {
      state.currentPermissions = permission;
    }
  },
  actions: {}
});
export default store;
