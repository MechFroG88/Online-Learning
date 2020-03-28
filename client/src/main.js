import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

// skeleton.css and styles
import '@/assets/scss/skeleton.css';
import '@/assets/scss/normalize.css';
import '@/assets/scss/style.scss';

// feather icons
import '@/assets/icons/feather/feather-webfont/feather.css';

// localizations
import VueI18n from 'vue-i18n';
import messages from '@/locale/messages.js';
Vue.use(VueI18n);
const i18n = new VueI18n({
  locale: 'en', // set locale
  messages, // set locale messages
});

// progress bar
import VueProgressBar from 'vue-progressbar';
Vue.use(VueProgressBar, {
  color: '#57b1ff',
  failedColor: 'f56c6c'
});

// carousel
import Carousel3d from 'vue-carousel-3d';
Vue.use(Carousel3d);

// meta data
import VueMeta from 'vue-meta';
Vue.use(VueMeta, {
  refreshOnceOnNavigation: true
});

// notification
import Notifications from 'vue-notification';
Vue.use(Notifications);

Vue.config.productionTip = false

export default new Vue({
  router,
  store,
  i18n,
  render: function (h) { return h(App) }
}).$mount('#app')
