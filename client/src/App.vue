<template>
  <div id="app">
    <navbar></navbar>
    <router-view/>
  </div>
</template>

<script>
import navbar from '@/components/navbar';
export default {
  components: {
    navbar
  }
}
</script>

<style lang="scss">
#app {
  font-family: 'Noto Sans SC', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  .header {
    position: fixed;
    top: 0; left: 0;
    width: 100%;
    z-index: 999;
    padding-top: 2rem;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #dfdfdf;
    section {
      margin: 0 2rem;

      @media screen and (min-width: 550px) {
        margin: 0 5rem;
      }
    }
  }
}
</style>
