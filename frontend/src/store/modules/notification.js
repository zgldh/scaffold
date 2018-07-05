/**
 * You should import APIs which you need. Just like:
 * import { RoleIndex } from '@/api/user'
 **/
import {
  notificationIndex,
  notificationDestroy,
  putReadAll,
  putRead,
  putUnread,
  getReadLatest
} from '@/api/notification'
import moment from 'moment'

const notification = {
  namespaced: true,
  state: {
    unread: 0,
    items: [],
    page: 1,
    hasMore: true
    /**
     State can hold some common data like enum items or values cross pages.
     list: []
     **/
  },
  getters: {},
  mutations: {
    nextPage: (state) => {
      state.page = state.page + 1
    },
    setPage: (state, page) => {
      state.page = page
    },
    appendItems: (state, items) => {
      state.items = state.items.concat(items)
    },
    prependItems: (state, items) => {
      state.items = items.concat(state.items)
    },
    setItems: (state, items) => {
      state.items = items
    },
    updateItemRead: (state, { notification, readAt }) => {
      var foundItem = state.items.find(item => item.id === notification.id)
      if (foundItem) {
        foundItem.read_at = readAt
      }
    },
    updateItemsReadAt: (state, readAt) => {
      state.items.forEach(item => {
        item.read_at = readAt
      })
    },
    removeItem: (state, notification) => {
      var index = state.items.findIndex(item => item.id === notification.id)
      if (index) {
        state.items.splice(index, 1)
      }
    },
    setUnread: (state, count) => {
      state.unread = count
    },
    setHasMore: (state, hasMore) => {
      state.hasMore = hasMore
    }
    /**
     Mutation function name should be SNAKE_CASE in uppercase.
     SET_LIST: (state, list) => {
        state.list = list
      }
     **/
  },
  actions: {
    async loadMore({ commit, state }) {
      var response = await notificationIndex({ page: state.page })
      commit('appendItems', response.data.items)
      commit('setUnread', response.data.unread)
      if (response.data.items && response.data.items.length) {
        commit('nextPage')
        commit('setHasMore', true)
      } else {
        commit('setHasMore', false)
      }
      return state.items
    },
    async loadLatest({ commit, state }) {
      var latestNotification = state.items.length ? state.items[0].created_at : '0000-00-00'
      var response = await getReadLatest(latestNotification)
      commit('prependItems', response.data.items)
      commit('setUnread', response.data.unread)
      return state.items
    },
    async readAll({ commit, state }) {
      await putReadAll()
      commit('setUnread', 0)
      commit('updateItemsReadAt', moment().format())
    },
    async read({ commit, state }, notification) {
      await putRead(notification.id)
      if (notification.read_at == null) {
        commit('setUnread', state.unread - 1)
      }
      commit('updateItemRead', { notification, readAt: '2012-12-12' })
    },
    async unread({ commit, state }, notification) {
      await putUnread(notification.id)
      if (notification.read_at != null) {
        commit('setUnread', state.unread + 1)
      }
      commit('updateItemRead', { notification, readAt: null })
    },
    async remove({ commit, state }, notification) {
      await notificationDestroy(notification.id)
      if (notification.read_at == null) {
        commit('setUnread', state.unread - 1)
      }
      commit('removeItem', notification)
    }
  }
}

export default notification
