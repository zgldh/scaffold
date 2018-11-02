import Layout from '../views/layout/Layout'
import i18n from '../lang'

/**
 * hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
 * alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
 *                                if not set alwaysShow, only more than one route under the children
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: noredirect           if `redirect:noredirect` will no redirct in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * permissions: []                Only who has one of these permissions can access/view this route
 *                                No permissions property means no guards.
 * meta : {
    title: 'title'               the name show in submenu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar,
  }
 **/
export default [{},
  {
    path: '/post/post',
    component: Layout,
    redirect: '/post/post/list',
    name: 'Post',
    permissions: ['Post@index'],
    meta: { title: () => i18n.t('post.title'), icon: 'fa-table' },
    children: [
      {
        path: 'list',
        name: 'Post List',
        permissions: ['Post@index'],
        component: () => import('@/views/Post/Post/List'),
        meta: { title: () => i18n.t('global.terms.list'), icon: 'fa-table' }
      },
      {
        hidden: true,
        path: ':id/edit',
        name: 'Edit Post',
        permissions: ['Post@update'],
        component: () => import('@/views/Post/Post/Editor'),
        meta: { title: () => i18n.t('global.terms.edit'), icon: 'fa-table' }
      },
      {
        hidden: true,
        path: 'create',
        name: 'Create Post',
        permissions: ['Post@store'],
        component: () => import('@/views/Post/Post/Editor'),
        meta: { title: () => i18n.t('global.terms.create'), icon: 'fa-table' }
      }
    ]
  }
  // Append More Routes. Don't remove me
]
