import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import Index from '../components/index.vue';
import Setting from '../components/Setting.vue';
import Share from '../components/Share.vue';

const router = new VueRouter({
  mode: 'history',
  base: '/schedule-new/',
  routes: [
    {
      path: '/setting',
      name: 'Setting',
      component: Setting,
    },
    {
      path: '/share',
      name: 'Share',
      component: Share,
    },
    {
      path: '/:date?',
      name: 'Index',
      component: Index,
    },
  ]
});

export default router;
