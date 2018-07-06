import router from './router'
import store from './store'
import NProgress from 'nprogress' // Progress 进度条
import 'nprogress/nprogress.css'// Progress 进度条样式
import { Message } from 'element-ui'
import { getToken } from '@/utils/auth' // 验权

const whiteList = ['/login', '/password', '/password/forget', '/password/reset'] // 不重定向白名单
router.beforeEach((to, from, next) => {
  if (to.path !== from.path) {
    NProgress.start()
  }
  if (getToken()) {
    if (to.path === '/login') {
      next({ path: '/' })
    } else {
      if (store.state.currentUser.name.length === 0) {
        store.dispatch('currentUser/GetInfo').then(res => { // 拉取用户信息
          router.applyDynamicRouters(store.state.currentUser.roles, store.state.currentUser.permissions)
          router.replace(to.fullPath)
          next()
        }).catch(() => {
          store.dispatch('currentUser/FedLogOut').then(() => {
            Message.error('验证失败,请重新登录')
            next({
              name: 'login',
              query: {
                redirect: to.params.redirect
              }
            })
          })
        })
      } else {
        next()
      }
    }
  } else {
    if (whiteList.indexOf(_.trimEnd(to.path, '/')) !== -1) {
      next()
    } else {
      next({
        name: 'login',
        query: {
          redirect: to.fullPath
        }
      })
      NProgress.done()
    }
  }
})

router.afterEach(() => {
  NProgress.done() // 结束Progress
})
