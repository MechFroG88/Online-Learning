<template>
  <div id="_login">
    <div class="username-form-group">
      <label for="username-label">{{ $t('login.fields.username') }}</label>
      <input class="u-full-width username-input" type="text" id="username-label">
    </div>
    <div class="password-form-group">
      <label for="password-label">{{ $t('login.fields.password') }}</label>
      <input class="u-full-width password-input" type="password" id="password-label">
    </div>
    <div class="submit-button">
      <input class="button button-primary" type="submit" :value="$t('login.title')">
    </div>
  </div>
</template>

<script>
export default {
  data: () => ({
    
  })
}
</script>

<style lang="scss">

</style>