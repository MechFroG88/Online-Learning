<template>
  <div id="_login">
    <form @submit.prevent="login">
      <div class="username-form-group">
        <label for="username">{{ $t('login.fields.username') }}</label>
        <input class="u-full-width username-input" type="text" id="username"
        autocomplete="username"
        v-model="user.username">
      </div>
      <div class="password-form-group">
        <label for="password">{{ $t('login.fields.password') }}</label>
        <input class="u-full-width password-input" type="password" id="password"
        autocomplete="current-password"
        v-model="user.password">
      </div>
      <div class="submit-button">
        <input class="button button-primary" type="submit" :value="$t('login.title')">
      </div>
    </form>
  </div>
</template>

<script>
import { mapMutations } from 'vuex';
import { userLogin } from '@/api/users';
export default {
  data: () => ({
    user: {
      username: '',
      password: ''
    }
  }),
  methods: {
    ...mapMutations({
      setUser: 'SET_USER'
    }),
    login() {
      let icArr = this.user.password.split("").filter(el => el != '-');
      if (/^[0-9]{12}$/.test(icArr.join(''))) {
        icArr.splice(8, 0, '-');
        icArr.splice(6, 0, '-');
      }
      userLogin({
        username: this.user.username, 
        password: icArr.join('')
      }).then((data) => {
        if (data.status == 200) {
          this.setUser(data.data);
          if (data.data.type == 1) this.$router.push('/home');
          else this.$router.push('/admin');
        }
      }).catch((err) => {
        if (err.response)
          alert(err.response.data);
        else alert(err.message);
      })
    },
  }
}
</script>

<style lang="scss">

</style>