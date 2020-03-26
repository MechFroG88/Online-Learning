<template>
  <div id="app">
    <navbar>
      <router-view/>
    </navbar>
  </div>
</template>

<script>
import navbar from '@/components/navbar';
export default {
  components: {
    navbar
  },
}
</script>

<style lang="scss">
#app {
  font-family: 'Noto Sans SC', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
}
</style>
