import Vue from 'vue'
import Router from 'vue-router'
import store from '../store'
import i18n from '../lang'

// in development-env not use lazy-loading, because lazy-loading too many pages will cause webpack hot update too slow. so only in production use lazy-loading;
// detail: https://panjiachen.github.io/vue-element-admin-site/#/lazy-loading

Vue.use(Router)

/* Layout */
import Layout from '../views/layout/Layout'
import SimpleLayout from '../views/layout/Simple'

import dynamicRouterMap from './dynamicRouterMap'
export const constantRouterMap = [
  {
    path: '/login',
    component: SimpleLayout,
    hidden: true,
    children: [
      {
        name: 'login',
        path: '',
        component: () => import('@/views/login/index'),
        hidden: true
      }
    ]
  },
  {
    path: '/password/',
    name: 'password pages',
    component: SimpleLayout,
    hidden: true,
    children: [
      {
        path: 'forget',
        component: () => import('@/views/password/forget'),
        hidden: true
      },
      {
        path: 'reset',
        component: () => import('@/views/password/reset'),
        hidden: true
      }
    ]
  },
  { path: '/404', component: () => import('@/views/404'), hidden: true },
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    name: 'dashboard',
    hidden: true,
    meta: { title: () => i18n.t('routes.dashboard') },
    children: [
      {
        path: 'dashboard',
        component: () => import('@/views/dashboard/index')
      },
      {
        path: 'my-profile',
        name: 'my-profile',
        meta: { title: () => i18n.t('routes.my_profile'), icon: 'table' },
        component: () => import('@/views/my-profile/index')
      }
    ]
  }
]

const wildCard404 = [
  { path: '*', redirect: '/404', hidden: true }
]

const router = new Router({
  // mode: 'history', //后端支持可开
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})
router.applyDynamicRouters = (roles, permissions) => {
  let routerMap = dynamicRouterMap
  if (!roles.includes(store.state.currentUser.superAdmin)) {
    routerMap = filterRoutes(routerMap, permissions)
  }
  router.addRoutes(routerMap)
  router.addRoutes(wildCard404)
  router.options.routes = router.options.routes.concat(routerMap)
  router.options.routes = router.options.routes.concat(wildCard404)
}

function filterRoutes(routerMap, permissions) {
  routerMap = routerMap.filter(item => {
    return permissionCheck(item, permissions)
  })
  return routerMap.map(item => {
    if (item.hasOwnProperty('children')) {
      item.children = filterRoutes(item.children, permissions)
    }
    return item
  })
}

function permissionCheck(item, permissions) {
  if (item.hasOwnProperty('permissions')) {
    let routerPermissions = item.permissions
    if (typeof item.permissions === 'string') {
      routerPermissions = [routerPermissions]
    }

    for (var index in permissions) {
      if (routerPermissions.includes(permissions[index])) {
        return true
      }
    }
    return false
  }
  return true
}

export default router
