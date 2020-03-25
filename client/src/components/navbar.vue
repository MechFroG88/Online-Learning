<template>
  <nav class="header" id="_navbar">
    <section>
      <h4>{{ $t('header.title') }}</h4>
    </section>
    <section class="settings dropdown">
      <button class="button-primary button dropdown-button">{{ $t('header.settings') }}</button>
      <div class="dropdown-menu">
        <div>
          <strong>
            <!-- {{ $t('header.loginStatus') }} name -->
            {{ $t('header.noLogin') }}
          </strong>
        </div>

        <hr style="margin: .5rem 0 1rem;">
        
        <button style="border: none">
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
</template>

<script>
export default {
  data() {
    this.$i18n.locale = 'en';
    return {
      locale: 'en',

    }
  },
  watch: {
    locale(val) {
      this.$i18n.locale = val;
    }
  }
}
</script>

<style lang="scss">

</style>