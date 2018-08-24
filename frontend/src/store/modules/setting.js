/**
 * You should import APIs which you need. Just like:
 * import { RoleIndex } from '@/api/user'
 **/
import { SettingIndex } from '@/api/setting'

const setting = {
  namespaced: true,
  state: {
    system: {}
    /**
     State can hold some common data like enum items or values cross pages.
     list: []
     **/
  },
  getters: {},
  mutations: {
    /**
     Mutation function name should be SNAKE_CASE in uppercase.
     setList: (state, list) => {
        state.list = list
      }
     **/
    setSystemItem(state, { key, value }) {
      state.system.key = value
    },
    setSystem(state, system) {
      state.system = system
    }
  },
  actions: {
    /**
     Actions should be able to async if it calls APIs.
     async LoadList({ commit, state }) {
        if (state.list.length) {
          return state.list
        } else {
          var response = await RoleIndex()
          commit('setList', response.data)
          return response.data
        }
      }
     **/
    async LoadSystem({ commit, state }) {
      if (JSON.stringify(state.system) !== '{}') {
        return state.system
      } else {
        var response = await SettingIndex()
        commit('setSystem', response.data)
        return response.data
      }
    }
  }
}

export default setting
