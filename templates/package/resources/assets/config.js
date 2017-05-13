import VueRouter from 'vue-router';
const MainList = () => System.import('./AppPage.vue');

const routes = [
  {path: '/', component: MainList},
];

const router = new VueRouter({
  routes
});

const config = {
  router: router
};

export {config};