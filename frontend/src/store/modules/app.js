import Cookies from 'js-cookie'
import moment from 'moment'
import { renderTitle } from '@/utils/browser'
import i18n from '@/lang'

function generateTitle(appState) {
  var title = i18n.t(appState.browserTitle.pageName)
  title += i18n.te(appState.browserTitle.separator) ? i18n.t(appState.browserTitle.separator) : appState.browserTitle.separator
  title += i18n.t('app_name')
  return title
}

const app = {
  state: {
    browserTitle: {
      pageName: '',
      separator: ' - '
    },
    sidebar: {
      opened: !+Cookies.get('sidebarStatus')
    },
    language: Cookies.get('language') || 'zh-CN'
  },
  mutations: {
    TOGGLE_SIDEBAR: state => {
      if (state.sidebar.opened) {
        Cookies.set('sidebarStatus', 1)
      } else {
        Cookies.set('sidebarStatus', 0)
      }
      state.sidebar.opened = !state.sidebar.opened
    },
    SET_LANGUAGE: (state, language) => {
      state.language = language
      Cookies.set('language', language)
      moment.locale(language)
      renderTitle(generateTitle(state))
    },
    SET_PAGE_NAME(state, pageName) {
      state.browserTitle.pageName = pageName
      renderTitle(generateTitle(state))
    },
    SET_SEPARATOR(state, separator) {
      state.browserTitle.separator = separator
      renderTitle(generateTitle(state))
    }
  },
  actions: {
    ToggleSideBar: ({ commit }) => {
      commit('TOGGLE_SIDEBAR')
    },
    setLanguage({ commit }, language) {
      commit('SET_LANGUAGE', language)
    }
  }
}

export default app
