import Vue from 'vue'
import VueI18n from 'vue-i18n'
import enLocale from 'element-ui/lib/locale/lang/en'
import zhLocale from 'element-ui/lib/locale/lang/zh-CN'
import deepmerge from 'deepmerge'

Vue.use(VueI18n)

export const i18n = new VueI18n({
  locale: window.Laravel.Locale, // set locale
})
i18n.mergeLocaleMessage(window.Laravel.Locale, window.Laravel.Languages)
i18n.mergeLocaleMessage('zh-CN', zhLocale)
i18n.mergeLocaleMessage('en', enLocale)

export function loadModuleLanguage (languageModule) {
  var languages = arguments
  return {
    beforeRouteEnter: function (to, from, next) {
      loadLanguages(languages).then(next)
    }
  }
}

var loadingModules = {}

function isModuleLoading (module) {
  if (loadingModules.hasOwnProperty(module)) {
    return true
  }
  return false
}
function getModuleLoadingPromise (module) {
  return loadingModules[module]
}
function setModuleLoading (module, promise) {
  loadingModules[module] = promise
}
function setModuleLoaded (module) {
  if (loadingModules.hasOwnProperty(module)) {
    delete loadingModules[module]
  }
}

function getCurrentMessages (locale, module) {
  var messages = i18n.getLocaleMessage(locale)
  if (messages.hasOwnProperty(module)) {
    return messages[module]
  }
  return {}
}

export function loadLanguages (languageModules) {
  if (languageModules.constructor === String) {
    languageModules = arguments
  }
  var message = i18n.getLocaleMessage(i18n.locale)
  var loads = []
  _.forEach(languageModules, module => {
    if (!message || !message.hasOwnProperty(module)) {
      if (isModuleLoading(module)) {
        loads.push(getModuleLoadingPromise(module))
      }
      else {
        var loadingPromise = axios.get('/lang/' + module)
        setModuleLoading(module, loadingPromise)
        loads.push(loadingPromise)
      }
    }
  })

  if (loads.length) {
    return Promise.all(loads).then(results => {
      _.forEach(results, function (result) {
        _.forEach(result.data, function (lauguageData, module) {
          setModuleLoaded(module)
          var langs = {}
          langs[module] = deepmerge(getCurrentMessages(i18n.locale, module), lauguageData)
          i18n.mergeLocaleMessage(i18n.locale, langs)
        })
      })
    }).catch(err => {
      throw err
    })
  }
  return Promise.resolve()
}
