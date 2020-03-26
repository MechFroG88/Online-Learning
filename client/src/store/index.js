import Vue from 'vue'
import Vuex from 'vuex'

import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    lang: '',
    user: {
      id: 1,
      username: "T00123",
      cn_name: "米莱",
      type: 1,
      class_subject: [
        {
          class: 1,
          subject: 1
        },
        {
          class: 2,
          subject: 1
        }
      ]
    },
  },
  mutations: {
    SET_LANG(state, lang) {
      state.lang = lang;
    }
  },
  actions: {
  },
  modules: {
  },
  plugins: [createPersistedState()]
})
