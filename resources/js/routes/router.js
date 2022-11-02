import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import Calendar from '../components/Calendar.vue';

const router = new VueRouter({
  mode: 'history',
  base: '/schedule-new/',
  routes: [
    {
      path: '/calendar',
      name: 'Calendar',
      component: Calendar,
    }
  ]
});

export default router;
