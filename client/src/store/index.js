import Vue from 'vue'
import Vuex from 'vuex'

import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    lang: '',
    user: {
      id: 0,
      username: "",
      cn_name: "",
      type: null,
      class_subject: []
    },
  },
  mutations: {
    SET_LANG(state, lang) {
      state.lang = lang;
    },
    SET_USER(state, user) {
      state.user = user;
    },
    RESET_USER(state) {
      state.user = {
        id: 0,
        username: "",
        cn_name: "",
        type: null,
        class_subject: []
      };
    }
  },
  actions: {
  },
  modules: {
  },
  plugins: [createPersistedState()]
})
