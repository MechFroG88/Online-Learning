<template>
  <div id="_navbar" :key="user.id">
    <nav class="header">
      <section>
        <h4>{{ $t('header.title') }}</h4>
      </section>
      <section class="settings dropdown">
        <button class="button-primary button dropdown-button" 
        style="display: flex; align-items: center;">
          {{ $t('header.settings') }} <i class="icon-cog" style="margin-left: .6rem;"></i>
        </button>
        <div class="dropdown-menu">
          <div>
            <strong>
              <div v-if="user.id">
                {{ $t('header.loginStatus') }} {{lang == 'cn' ? user.cn_name : user.en_name}}
              </div>
              <div v-else>
                {{ $t('header.noLogin') }}
              </div>
            </strong>
          </div>

          <hr style="margin: .5rem 0 1rem;">
          
          <button style="border: none" @click="logout">
            {{ $t('header.logout') }}
          </button>
          
          <hr style="margin: 0 0 1.5rem;">
          
          <label for="language-select">{{ $t('header.language') }}</label>
          <select v-model="locale" id="langauge-select">
            <option value="en">English</option>
            <option value="cn">中文</option>
            <option value="bm">Bahasa Malaysia</option>
          </select>
        </div>
      </section>
    </nav>
    <div class="content">
      <slot></slot>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
import { userLogout } from '@/api/users';
export default {
  data: () => ({
    locale: '',
  }),
  methods: {
    ...mapMutations({ 
      setLang: 'SET_LANG',
      resetUser: 'RESET_USER'
    }),
    logout() {
      if (this.$route.name != 'login') 
        userLogout().then((data) => {
          if (data.status == 200) {
            localStorage.clear();
            this.resetUser();
            this.$router.push('/');
          }
        })
    }
  },
  mounted() {
    this.locale =  this.lang || 'en';
    this.$i18n.locale = this.locale;
  },
  watch: {
    locale(val) {
      this.$i18n.locale = val;
      this.setLang(val);
    }
  },
  computed: {
    ...mapState({ 
      lang: 'lang',
      user: 'user'
    }),
  }
}
</script>

<style lang="scss">

</style>