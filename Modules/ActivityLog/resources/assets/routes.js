export default [
  {
    name: 'module_activity_log.title,global.terms.list',
    path: '/activity_log/list',
    component: () => System.import('./ListPage.vue')
  },
]