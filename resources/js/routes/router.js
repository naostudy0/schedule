import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import Index from '../components/index.vue';
import Setting from '../components/Setting.vue';

const router = new VueRouter({
  mode: 'history',
  base: '/schedule-new/',
  routes: [
    {
      path: '/',
      name: 'Index',
      component: Index,
    },
    {
      path: '/setting',
      name: 'Setting',
      component: Setting,
    }
  ]
});

export default router;
