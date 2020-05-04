<template>
  <div id="_navbar" :key="user.id">
    <nav class="header">
      <section class="title" @click="$router.push('/login')">
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

          <template v-if="user.id">
            <button style="border: none" @click="logout">
              {{ $t('header.logout') }}
            </button>

            <button style="border: none" @click="$router.push('/change_pass')">
              {{ $t('header.changePassword') }}
            </button>
            
            <hr style="margin: 0 0 1.5rem;">
          </template>
          
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
      resetUser: 'RESET_USER',
      resetHome: 'RESET_HOME'
    }),
    logout() {
      if (this.$route.name != 'login') 
        userLogout().then((data) => {
          if (data.status == 200) {
            this.$router.push('/login');
            this.resetHome();
            this.resetUser();
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