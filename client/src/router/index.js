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
      permissions: [0, 1]
    },
    component: () => import('@/views/homepage.vue')
  },
  {
    path: '/admin/',
    name: 'admin',
    meta: {
      permissions: [0]
    },
    component: () => import('@/views/admin.vue'),
    children: [
      {
        path: '',
        redirect: { name: 'adminUser' }
      },
      {
        path: 'event',
        name: 'adminEvent',
        meta: {
          permissions: [0]
        },
        component: () => import('@/views/admin_pages/event.vue')
      },
      {
        path: 'user',
        name: 'adminUser',
        meta: {
          permissions: [0]
        },
        component: () => import('@/views/admin_pages/user.vue')
      },
      {
        path: 'period',
        name: 'adminPeriod',
        meta: {
          permissions: [0]
        },
        component: () => import('@/views/admin_pages/period.vue')
      },
      {
        path: 'class',
        name: 'adminClass',
        meta: {
          permissions: [0]
        },
        component: () => import('@/views/admin_pages/class.vue')
      },
      {
        path: 'subject',
        name: 'adminSubject',
        meta: {
          permissions: [0]
        },
        component: () => import('@/views/admin_pages/subject.vue')
      },
      {
        path: 'day',
        name: 'adminDay',
        meta: {
          permissions: [0]
        },
        component: () => import('@/views/admin_pages/day.vue')
      },
      {
        path: 'class_user',
        name: 'adminClassUser',
        meta: {
          permissions: [0]
        },
        component: () => import('@/views/admin_pages/class_user.vue')
      }
    ]
  },
  {
    path: '/admin/choice/master',
    name: 'masterChoice',
    meta: {
      permissions: [0]
    },
    component: () => import('@/views/homepage.vue')
  },
  {
    path: '/admin/choice',
    name: 'choice',
    meta: {
      permissions: [0]
    },
    component: () => import('@/views/homepage.vue')
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
  console.log(to);
  if (to.meta.permissions != undefined) {
    if (to.meta.permissions.indexOf(store.state.user.type) == -1) {
      if (store.state.user.type == 0) next({ name: 'admin' });
      else if (store.state.user.type == 1) next({ name: 'home' });
      else next({ name: 'login' });
    }
  }
  next();
});

export default router
