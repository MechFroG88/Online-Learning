<template>
  <div id="_forgotPass">
    <div class="lock"></div>
    <div class="instructions"><b>{{ $t('forgot.title') }}</b></div>
    <div class="instructions"><small>{{ $t('forgot.subtitle') }}</small></div>
    <form @submit.prevent="reset">
      <div class="username-form-group container">
        <label class="columns two" for="username">{{ $t('forgot.username') }}</label>
        <input class="username-input columns ten" type="text" id="username"
        autocomplete="username"
        v-model="user.username">
      </div>
      <div class="email-form-group container">
        <label class="columns two" for="email">{{ $t('forgot.email') }}</label>
        <input class="email-input columns ten" type="email" id="email"
        autocomplete="email"
        v-model="user.email">
      </div>
      <div class="tnc-form-group container">
        <input type="checkbox" name="tnc" id="tnc" v-model="agree">
        <div for="tnc">
          {{ $t('forgot.agree') }}
          <a href="https://www.termsfeed.com/live/40226656-d99a-40c8-a678-1f35a214341d">
            {{ $t('forgot.tnc') }}
          </a>
        </div>
      </div>
      <div class="submit container">
        <input class="button-primary" type="submit" :value="$t('forgot.submit')">
      </div>
    </form>
  </div>
</template>

<script>
import { resetPassword } from '@/api/password';
export default {
  data: () => ({
    user: {
      username: '',
      email: ''
    },
    agree: false
  }),
  methods: {
    reset() {
      if (this.agree) {
        resetPassword(this.user).then((data) => {
          if (data.status == 200) {
            this.$notify({
              type: 'success',
              title: 'Password Reset Successfully',
              text: 'Please check your email for the new password'
            });
            this.$router.push('/login');
          }
        }).catch((err) => {
          if (err.response)
            this.$notify({
              type: 'error',
              title: 'Error reset password',
              text: err.response.data
            })
          else
            this.$notify({
              type: 'error',
              title: 'Error reset password',
              text: err.message
            })
        })
      }
      else {
        this.$notify({
          type: 'warning',
          title: 'You must agree to the terms and conditions.'
        })
      }
    }
  },
}
</script>

<style lang="scss">

</style>