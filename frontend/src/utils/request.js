import axios from 'axios'
import { Message, MessageBox } from 'element-ui'
import store from '../store'
import i18n from '../lang'
import { getToken } from '@/utils/auth'
import _ from 'lodash'
import NProgress from 'nprogress' // Progress 进度条

NProgress.configure({ showSpinner: false })

// 创建axios实例
const service = axios.create({
  baseURL: process.env.BASE_API, // api的base_url
  timeout: 15000 // 请求超时时间
})

// request拦截器
service.interceptors.request.use(async config => {
  await store.dispatch('currentUser/PromiseTokenIsLoaded')

  NProgress.start()

  // Language setting
  config.headers['Accept-Language'] = store.state.app.language

  if (store.state.currentUser.token) {
    config.headers['Authorization'] = 'bearer ' + getToken() // 让每个请求携带自定义 token 请根据实际情况自行修改
  }
  if (config.params && config.params.constructor === String) {
    config.url += '?' + config.params
    config.params = null
  }
  return config
}, error => {
  // Do something with request error
  console.log(error) // for debug
  Promise.reject(error)
})

// respone拦截器
service.interceptors.response.use(
  response => {
    NProgress.done() // 结束Progress
    /**
     * code为非20000是抛错 可结合自己业务进行修改
     */
    const res = response.data
    if (res.success === false) {
      Message({
        message: res.message,
        type: 'error',
        duration: 3 * 1000
      })

      // 50008:非法的token; 50012:其他客户端登录了;  50014:Token 过期了;
      if (response.code === 401 || response.code === 50012 || response.code === 50014) {
        onUnauthenticated()
      }
      return Promise.reject('error')
    } else {
      return response.data
    }
  },
  error => {
    NProgress.done() // 结束Progress
    console.log('err' + error)// for debug
    let message = _.get(error, 'response.data.message')
    message = message || _.get(error, 'response.data.error.message')
    message = message || error.message
    if (message === 'Unauthenticated.') {
      onUnauthenticated()
    } else {
      var response = error.response
      if (response.status === 422) {
        // Do nothing
        return Promise.reject(response)
      } else {
        Message({
          message: message,
          type: 'error',
          duration: 3 * 1000
        })
      }
    }
    return Promise.reject(error)
  }
)

function onUnauthenticated() {
  MessageBox.confirm(i18n.t('messages.session_expired.text'), i18n.t('messages.session_expired.title'), {
    confirmButtonText: i18n.t('messages.session_expired.confirm'),
    cancelButtonText: i18n.t('messages.session_expired.cancel'),
    type: 'warning'
  }).then(() => {
    store.dispatch('currentUser/FedLogOut').then(() => {
      location.reload()// 为了重新实例化vue-router对象 避免bug
    })
  })
}

export default service
