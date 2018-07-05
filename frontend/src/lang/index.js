/**
 * Created by zhangwb-pc on 03/28/2018.
 */
// i18n
import Vue from 'vue'
import VueI18n from 'vue-i18n'
import Cookies from 'js-cookie'
import _ from 'lodash'
import moment from 'moment'

import elementEnLocale from 'element-ui/lib/locale/lang/en'
import elementZhLocale from 'element-ui/lib/locale/lang/zh-CN'

import enLang from './en'
import zhLang from './zh-CN'

import dumpedLanguages from './languages'

Vue.use(VueI18n)

export const defaultLocale = Cookies.get('language') || 'zh-CN'

moment.locale(defaultLocale)

// i18n
export const messages = _.merge(dumpedLanguages, {
  'en': {
    ...enLang,
    ...elementEnLocale
  },
  'zh-CN': {
    ...zhLang,
    ...elementZhLocale
  }
})

// Create VueI18n instance with options
const i18n = new VueI18n({
  locale: defaultLocale, // set locale
  messages // set locale messages
})

export default i18n
