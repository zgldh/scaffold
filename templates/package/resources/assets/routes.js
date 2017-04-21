export default [
  {path: '/$MODEL_NAME_LOWER$', component: () => System.import('./AppPage.vue')},
  {path: '/$MODEL_NAME_LOWER$/create', component: () => System.import('./EditorPage.vue')},
  {path: '/$MODEL_NAME_LOWER$/:id/edit', component: () => System.import('./EditorPage.vue')},
];