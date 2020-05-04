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
      <div class="forget-form-group" @click="$router.push('/forgot_pass')">
        {{ $t('login.forgotPass') }}
      </div>
      <div class="submit-button">
        <input class="button button-primary" type="submit" :value="$t('login.title')">
      </div>
    </form>
  </div>
</template>

<script>
import { mapMutations, mapState } from 'vuex';
import { userLogin } from '@/api/users';
export default {
  data: () => ({
    user: {
      username: '',
      password: ''
    }
  }),
  mounted() {
    if (this.local_user.id) {
      if (this.local_user.type == 1) this.$router.push('/home');
      else this.$router.push('/admin');
    }
  },
  methods: {
    ...mapMutations({
      setUser: 'SET_USER'
    }),
    login() {
      userLogin(this.user).then((data) => {
        if (data.status == 200) {
          this.setUser(data.data);
          this.$nextTick(() => {
            if (data.data.username == 'T00110')
              this.$notify('亲爱的刘老师，你好！！！')
            if (data.data.username == 'T00139')
              this.$notify('最帅的葱哥，你好！！！')
            if (data.data.type == 1) this.$router.push('/home');
            else this.$router.push('/admin');
          })
        }
      }).catch((err) => {
        if (err.response)
          this.$notify({
            type: 'error',
            title: 'Error login',
            text: err.response.data
          })
        else
          this.$notify({
            type: 'error',
            title: 'Error login',
            text: err.message
          })
      })
    },
  },
  computed: {
    ...mapState({
      local_user: 'user'
    })
  }
}
</script>

<style lang="scss">

</style>