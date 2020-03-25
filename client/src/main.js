import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

import '@/assets/scss/skeleton.css';
import '@/assets/scss/style.scss';

import VueI18n from 'vue-i18n';
import messages from '@/locale/messages.js';
Vue.use(VueI18n)
const i18n = new VueI18n({
  locale: 'en', // set locale
  messages, // set locale messages
})

Vue.config.productionTip = false

new Vue({
  router,
  store,
  i18n,
  render: function (h) { return h(App) }
}).$mount('#app')
