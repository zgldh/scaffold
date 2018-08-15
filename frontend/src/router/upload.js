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
export default [
  {
    path: '/upload',
    component: Layout,
    redirect: '/upload/list',
    name: 'Upload',
    permissions: ['Upload@index'],
    meta: { title: () => i18n.t('upload.title'), icon: 'fa-cogs' },
    children: [
      {
        path: 'list',
        name: 'Upload List',
        permissions: ['Upload@index'],
        component: () => import('@/views/upload/List'),
        meta: {
          title: () => i18n.t('components.list_title', { name: i18n.t('upload.title') }),
          icon: 'fa-upload'
        }
      },
      {
        hidden: true,
        path: ':id/edit',
        name: 'Edit Upload',
        permissions: ['Upload@update'],
        component: () => import('@/views/upload/Editor'),
        meta: {
          title: () => i18n.t('global.terms.edit'),
          icon: 'fa-user'
        }
      }
    ]
  }
]
