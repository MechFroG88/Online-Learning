import Vue from 'vue'
import Vuex from 'vuex'

import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    lang: 'cn',
    user: {
      id: 0,
      username: "",
      cn_name: "",
      en_name: "",
      email: "",
      type: null,
      class_user: [],
      class: {}
    },
    sub_user: {
      id: 0,
      username: "",
      cn_name: "",
      en_name: "",
      email: "",
      type: null,
      class_user: [],
      class: {}
    },
    home: {
      event: {
        id: 0
      },
      class: 0,
      index: 0,
    }
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
        en_name: "",
        email: "",
        type: null,
        class_user: [],
        class: {}
      };
    },
    SET_SUBUSER(state, sub_user) {
      state.sub_user = sub_user;
    },
    RESET_SUBUSER(state) {
      state.sub_user = {
        id: 0,
        username: "",
        cn_name: "",
        en_name: "",
        email: "",
        type: null,
        class_user: [],
        class: {}
      };
    },
    RESET_HOME(state) {
      state.home = {
        event: {
          id: 0
        },
        class: 0,
        index: 0,
      };
    },
    SET_CLASS(state, classs) {
      state.home.class = classs;
    },
    SET_EVENT(state, event) {
      state.home.event = event;
    },
    SET_INDEX(state, index) {
      state.home.index = index;
    }
  },
  actions: {
  },
  modules: {
  },
  plugins: [createPersistedState()]
})
