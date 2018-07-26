/**
 * You should import APIs which you need. Just like:
 * import { RoleIndex } from '@/api/user'
 **/

const activityLog = {
  namespaced: true,
  state: {
    actionsNotExpandable: [
      'logout',
      'updated-password'
    ]
  },
  getters: {},
  mutations: {},
  actions: {}
}

export default activityLog
