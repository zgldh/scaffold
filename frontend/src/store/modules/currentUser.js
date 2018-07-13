import { login, logout, getInfo, refreshToken } from '@/api/login'
import { putMobile, putGender, putPassword } from '@/api/user'
import {
  getToken,
  setToken,
  removeToken,
  initTokenRefresher,
  removeTokenRefresher
} from '@/utils/auth'
import { forget, reset } from '@/api/password'

const currentUser = {
  namespaced: true,
  state: {
    token: getToken(),
    name: '',
    email: '',
    gender: '',
    mobile: '',
    avatar: '',
    createdAt: '',
    roles: [],
    permissions: [],
    superAdmin: 'super-admin',
    tokenLoading: false
  },
  getters: {
    hasPermission: (state) => (permission) => {
      if (state.roles.indexOf(state.superAdmin) !== -1) {
        return true
      }
      const index = state.permissions.findIndex(item => {
        return item === permission
      })
      return index >= 0
    },
    isLogin: (state) => {
      return !!state.token
    }
  },
  mutations: {
    SET_TOKEN: (state, token) => {
      state.token = token
    },
    SET_NAME: (state, name) => {
      state.name = name
    },
    SET_EMAIL: (state, email) => {
      state.email = email
    },
    SET_GENDER: (state, gender) => {
      state.gender = gender
    },
    SET_MOBILE: (state, mobile) => {
      state.mobile = mobile
    },
    SET_AVATAR: (state, avatar) => {
      state.avatar = avatar
    },
    SET_CREATED_AT: (state, created_at) => {
      state.createdAt = created_at
    },
    SET_ROLES: (state, roles) => {
      state.roles = roles
    },
    SET_PERMISSIONS: (state, permissions) => {
      state.permissions = permissions
    }
  },

  actions: {
    // 修改当前用户手机
    UpdateCurrentUserMobile({ commit }, mobile) {
      return putMobile({ mobile }).then(response => {
        const data = response
        commit('SET_MOBILE', mobile)
        return data
      }).catch(error => {
        throw error
      })
    },
    // 修改当前用户性别
    UpdateCurrentUserGender({ commit }, gender) {
      return putGender({ gender }).then(response => {
        const data = response
        commit('SET_GENDER', gender)
        return data
      }).catch(error => {
        throw error
      })
    },
    // 修改当前用户密码
    UpdateCurrentUserPassword({ commit }, { oldPassword, password, passwordRepeat }) {
      return putPassword(
        {
          oldPassword: oldPassword,
          password: password,
          password_confirmation: passwordRepeat
        }
      ).then(response => {
        const data = response
        return data
      }).catch(error => {
        throw error
      })
    },
    // 登录
    Login(store, userInfo) {
      const email = userInfo.email.trim()
      return login(email, userInfo.password).then(response => {
        const data = response
        setToken(data.token)
        store.commit('SET_TOKEN', data.token)
        return data
      }).catch(error => {
        throw error
      })
    },
    // 刷新 Token
    async RefreshToken({ commit, store }) {
      store.tokenLoading = true
      try {
        const response = await refreshToken()
        setToken(response.token)
        commit('SET_TOKEN', response.token)
        return response
      } catch (e) {
        throw e
      } finally {
        store.tokenLoading = false
      }
    },
    // 获取用户信息
    GetInfo(store) {
      return getInfo(store.state.token).then(response => {
        const data = response.data
        store.commit('SET_PERMISSIONS', data.permissions)
        store.commit('SET_ROLES', data.roles)
        store.commit('SET_NAME', data.name)
        store.commit('SET_EMAIL', data.email)
        store.commit('SET_GENDER', data.gender)
        store.commit('SET_MOBILE', data.mobile)
        store.commit('SET_AVATAR', data.avatar)
        store.commit('SET_CREATED_AT', data.created_at)
        initTokenRefresher(store)
        return response
      }).catch(error => {
        throw error
      })
    },

    // 登出
    LogOut({ commit, state }) {
      return logout(state.token).then(() => {
        commit('SET_TOKEN', '')
        commit('SET_ROLES', [])
        commit('SET_PERMISSIONS', [])
        removeToken()
        removeTokenRefresher()
        return true
      }).catch(error => {
        throw (error)
      })
    },

    // 前端 登出
    FedLogOut({ commit }) {
      return new Promise(resolve => {
        commit('SET_TOKEN', '')
        removeToken()
        removeTokenRefresher()
        resolve()
      })
    },

    // 忘记密码
    Forget({ commit }, formInfo) {
      const email = formInfo.email.trim()
      return forget(email).then(response => {
        const data = response
        return data
      }).catch(error => {
        throw error
      })
    },

    // 重置密码
    Reset({ commit }, formInfo) {
      const email = formInfo.email.trim()
      return reset(email, formInfo.password, formInfo.password_confirmation, formInfo.token).then(response => {
        const data = response
        return data
      }).catch(error => {
        throw error
      })
    },

    async PromiseTokenIsLoaded({ commit, dispatch, state }) {
      while (state.tokenLoading) {
        // waiting
      }
      return true
    }
  }
}

export default currentUser
