import post from './modules/post'
import notification from './modules/notification'
import Vue from 'vue'
import Vuex from 'vuex'
import app from './modules/app'
import activityLog from './modules/activityLog'
import currentUser from './modules/currentUser'
import user from './modules/user'
import setting from './modules/setting'
import getters from './getters'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    // Append More Stores. Don't remove me
    post,
    notification,
    app,
    currentUser,
    user,
    activityLog,
    setting
  },
  getters
})

export default store
