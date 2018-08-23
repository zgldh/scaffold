import store from '@/store'
import _ from 'lodash'

export function updateTitle(pageNameLang, separator) {
  if (separator) {
    store.commit('SET_SEPARATOR', separator, { root: true })
  }
  store.commit('SET_PAGE_NAME', pageNameLang, { root: true })
}

export function renderTitle(title) {
  _.set(window, 'document.title', title)
}
