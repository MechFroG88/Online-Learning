<template>
  <div id="_changePass">
    <form @submit.prevent="changePwd">
      <div class="old-form-group form-group container">
        <label for="old_password">{{ $t('change.old_password') }}</label>
        <input class="old_password-input u-full-width" type="text" id="old_password"
        autocomplete="old_password"
        v-model="data.old_password">
      </div>
      <div class="new-form-group form-group container">
        <label for="new_password">{{ $t('change.new_password') }}</label>
        <input class="new_password-input u-full-width" type="text" id="new_password"
        autocomplete="new_password"
        @keyup="check" v-model="data.new_password">
      </div>
      <div class="confirm-form-group form-group container">
        <label for="confirm">{{ $t('change.confirm') }}</label>
        <input class="confirm-input u-full-width" type="text" id="confirm"
        @keyup="check" v-model="data.new_password_confirmation">
        <small class="text-error" v-if="wrong">{{ $t('change.wrong') }}</small>
      </div>

      <div class="container submit">
        <input class="button-primary" type="submit" :value="$t('modal.submit')">
      </div>
    </form>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { changePassword } from '@/api/password';
export default {
  mounted() {
    this.data.username = this.user.username;
  },
  data: () => ({
    data: {
      username: null,
      old_password: '',
      new_password: '',
      new_password_confirmation: '',
    },
    wrong: false,
  }),
  methods: {
    check() {
      this.wrong = this.data.new_password !== this.data.new_password_confirmation;
    },
    changePwd() {
      if (this.wrong) this.$notify({
        type: 'warning',
        title: 'Password does not match',
        text: 'Please make sure that your new password is confirmed.'
      })
      else {
        changePassword(this.data).then((data) => {
          if (data.status == 200) {
            this.$notify({
              type: 'success',
              title: 'Password changed successfully'
            });

            if (this.user.type == 0) this.$router.push({ name: 'admin' });
            else if (this.user.type == 1) this.$router.push({ name: 'home' });
            else this.$router.push({ name: 'login' });
          }
        }).catch((err) => {
          if (err.response)
            this.$notify({
              type: 'error',
              title: 'Error changing password',
              text: err.response.data
            })
          else
            this.$notify({
              type: 'error',
              title: 'Error changing password',
              text: err.message
            })
        })
      }
    }
  },
  computed: {
    ...mapState({
      user: 'user'
    })
  }
}
</script>

<style lang="scss">

</style>