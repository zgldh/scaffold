import Vue from 'vue'

import 'normalize.css/normalize.css'// A modern alternative to CSS resets

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'

import '@/styles/index.scss' // global css
import 'font-awesome/scss/font-awesome.scss'
import 'ionicons/dist/scss/ionicons.scss'
import VueClipboard from 'vue-clipboard2'

import App from './App'
import router from './router'
import store from './store'
import i18n from './lang'

import '@/icons' // icon
import '@/permission' // permission control
import globalMixin from '@/mixins/global' // Global Mixin
import FormItem from '@/components/FormItem'

// Common components
Vue.component('FormItem', FormItem)

Vue.use(ElementUI, {
  i18n: (key, value) => i18n.t(key, value)
})

Vue.mixin(globalMixin)

Vue.use(VueClipboard)

Vue.config.productionTip = false

new Vue({
  el: '#app',
  router,
  store,
  i18n,
  template: '<App/>',
  components: { App }
})
