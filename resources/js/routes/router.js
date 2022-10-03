import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

import Login from "../components/Login.vue";
import About from "../components/About.vue";

const router = new VueRouter({
  mode: "history",
  base: '/schedule-new/',
  routes: [
    {
      path: "",
      name: "Login",
      component: Login,
    },
    {
      path: "/about",
      name: "About",
      component: About,
    }
  ]
});

export default router;
