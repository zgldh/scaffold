import Vue from 'vue'
import AutoIcon from '@/components/AutoIcon'// svg组件
import SvgIcon from '@/components/SvgIcon'// svg组件

// register globally
Vue.component('auto-icon', AutoIcon)
Vue.component('svg-icon', SvgIcon)

const requireAll = requireContext => requireContext.keys().map(requireContext)
const req = require.context('./svg', false, /\.svg$/)
requireAll(req)
