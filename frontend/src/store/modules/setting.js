/**
 * You should import APIs which you need. Just like:
 * import { RoleIndex } from '@/api/user'
 **/
import { SettingIndex, SettingUpdate, SettingUpdateItem, SettingReset } from '@/api/setting'
import _ from 'lodash'

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
    setSystemItem(state, { name, value }) {
      state.system[name] = value
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
    async LoadSystem({ commit, state }, configuration) {
      const getDefault = _.get(configuration, 'default', false)
      const force = _.get(configuration, 'force', false)
      if (!force && !getDefault && JSON.stringify(state.system) !== '{}') {
        return JSON.parse(JSON.stringify(state.system))
      } else {
        const params = {}
        if (getDefault) {
          params.d = 1
        }
        var response = await SettingIndex(params)
        commit('setSystem', response.data)
        return JSON.parse(JSON.stringify(response.data))
      }
    },
    async SaveSystemItem({ commit, state }, { name, value }) {
      try {
        var response = await SettingUpdateItem(name, value)
        commit('setSystemItem', response.data.name, response.data.value)
        return JSON.parse(JSON.stringify(response.data))
      } catch (e) {
        throw e
      }
    },
    async SaveSystem({ commit, state }, data) {
      const payload = getChangedSettings(state.system, data)
      if (_.isEmpty(payload)) {
        return data
      }
      try {
        var response = await SettingUpdate(payload)
        commit('setSystem', response.data)
        return JSON.parse(JSON.stringify(response.data))
      } catch (e) {
        throw e
      }
    },
    async ResetSystem({ commit, state }) {
      try {
        var response = await SettingReset()
        commit('setSystem', response.data)
        return JSON.parse(JSON.stringify(response.data))
      } catch (e) {
        throw e
      }
    }
  }
}

function getChangedSettings(oldSettings, newSetting) {
  return _.pickBy(newSetting, (value, name) => JSON.stringify(value) !== JSON.stringify(_.get(oldSettings, name)))
}

export default setting
