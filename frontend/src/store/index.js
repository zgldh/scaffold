import notification from './modules/notification'
import Vue from 'vue'
import Vuex from 'vuex'
import app from './modules/app'
import activityLog from './modules/activityLog'
import currentUser from './modules/currentUser'
import user from './modules/user'
import setting from './modules/setting'
import tagsView from './modules/tagsView'
import getters from './getters'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    // Append More Stores. Don't remove me
    notification,
    app,
    currentUser,
    user,
    activityLog,
    setting,
    tagsView
  },
  getters
})

export default store
