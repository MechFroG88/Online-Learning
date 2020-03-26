<template>
  <div id="_modal" class="modal"
  :class="{ 'active': active }">
    <div class="modal-container">
      <span class="close" @click="active = false;">&times;</span>
      <slot name="body"></slot>
    </div>
    <a class="modal-overlay" @click="active = false"></a>
  </div>
</template>

<script>
export default {
  data: () => ({
    active: false
  })
}
</script>

<style lang="scss">

</style>