<template>
  <div id="_login">
    <form @submit.prevent="login">
      <div class="username-form-group">
        <label for="username-label">{{ $t('login.fields.username') }}</label>
        <input class="u-full-width username-input" type="text" id="username-label"
        v-model="user.username">
      </div>
      <div class="password-form-group">
        <label for="password-label">{{ $t('login.fields.password') }}</label>
        <input class="u-full-width password-input" type="password" id="password-label"
        v-model="user.password">
      </div>
      <div class="submit-button">
        <input class="button button-primary" type="submit" :value="$t('login.title')">
      </div>
    </form>
  </div>
</template>

<script>
import { userLogin } from '@/api/users';
export default {
  data: () => ({
    user: {
      username: '',
      password: ''
    }
  }),
  methods: {
    login() {
      userLogin(this.user).then(({ data }) => {
        console.log(data);
      })
    },
  }
}
</script>

<style lang="scss">

</style>