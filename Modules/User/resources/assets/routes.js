export default [
  {
    name: 'user.title,global.terms.list',
    path: '/user/list',
    component: () => System.import('./User/ListPage.vue')
  },
  {
    name: 'user.title,global.terms.create',
    path: '/user/create',
    component: () => System.import('./User/EditorPage.vue')
  },
  {
    name: 'user.title,global.terms.edit',
    path: '/user/:id/edit',
    component: () => System.import('./User/EditorPage.vue')
  },

  {
    name: 'role.title,global.terms.list',
    path: '/user/role',
    component: () => System.import('./Role/ListPage.vue')
  },
  {
    name: 'role.title,global.terms.create',
    path: '/user/role/create',
    component: () => System.import('./Role/EditorPage.vue')
  },
  {
    name: 'role.title,global.terms.edit',
    path: '/user/role/:id/edit',
    component: () => System.import('./Role/EditorPage.vue')
  },

  {
    name: 'permission.title,global.terms.list',
    path: '/user/permission',
    component: () => System.import('./Permission/ListPage.vue')
  },
  {
    name: 'permission.title,global.terms.create',
    path: '/user/permission/create',
    component: () => System.import('./Permission/EditorPage.vue')
  },
  {
    name: 'permission.title,global.terms.edit',
    path: '/user/permission/:id/edit',
    component: () => System.import('./Permission/EditorPage.vue')
  },
]