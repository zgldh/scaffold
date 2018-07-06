import { RoleIndex, PermissionIndex, PermissionDestroy } from '@/api/user'

const user = {
  namespaced: true,
  state: {
    roles: [],
    permissions: [],
    defaultActions: [
      'destroy',
      'index',
      'show',
      'store',
      'update'
    ]
  },
  getters: {},
  mutations: {
    SET_ROLES: (state, roles) => {
      state.roles = roles
    },
    REMOVE_PERMISSION: (state, permissionId) => {
      var index = state.permissions.findIndex(item => item.id === permissionId)
      if (index >= 0) {
        state.permissions.splice(index, 1)
      }
    },
    SET_PERMISSIONS: (state, permissions) => {
      state.permissions = permissions
    },
    SET_PERMISSION: (state, permission) => {
      var index = state.permissions.findIndex(item => item.id === permission.id)
      if (index >= 0) {
        state.permissions.splice(index, 1, permission)
      }
    },
    ADD_PERMISSION: (state, permission) => {
      state.permissions.push(permission)
    }
  },

  actions: {
    async LoadRoles({ commit, state }, force) {
      if (state.roles.length && !force) {
        return state.roles
      } else {
        var response = await RoleIndex()
        commit('SET_ROLES', response.data)
        return response.data
      }
    },
    async LoadPermissions({ commit, state }, force) {
      if (state.permissions.length && !force) {
        return state.permissions
      } else {
        var response = await PermissionIndex()
        commit('SET_PERMISSIONS', response.data)
        return response.data
      }
    },
    async removePermission({ commit, state }, permissionId) {
      var response = await PermissionDestroy(permissionId)
      commit('REMOVE_PERMISSION', permissionId)
      return response.data
    },
    setLanguage: {
      root: true,
      handler: (store, payload) => {
        if (store.rootGetters['currentUser/isLogin']) {
          store.dispatch('LoadPermissions', true)
        }
      }
    }
  }
}

export default user
