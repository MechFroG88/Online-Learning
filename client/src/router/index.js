import Vue from 'vue'
import store from '@/store';
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    redirect: { name: 'login' }
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/login.vue')
  },
  {
    path: '/home',
    name: 'home',
    meta: {
      permissions: 1
    },
    component: () => import('@/views/homepage.vue')
  },
  {
    path: '/admin',
    name: 'admin',
    meta: {
      permissions: 0
    },
    component: () => import('@/views/admin.vue')
  },
  {
    path: '/*',
    redirect: { name: 'login' }
  }
];

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  linkExactActiveClass: 'active',
  routes
});

router.beforeEach((to, from, next) => {
  if (to.meta.permissions != undefined) {
    if (to.meta.permissions != store.state.user.type) {
      if (store.state.user.type == 0) next({ name: 'admin' });
      else if (store.state.user.type == 1) next({ name: 'home' });
      else next({ name: 'login' });
    }
  }
  next();
});

export default router
