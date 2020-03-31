<template>
  <div id="_admin">
    <nav>
      <h5>
        {{ $t('admin.navigate') }} <i class="icon-location" style="margin-left: 1rem;"></i>
      </h5>
      <ul>
        <li>
          <router-link tag="div" 
          :to="{name: 'adminEvent'}"
          :class="{'active': $route.name == 'adminEvent'}">{{ $t('admin.events') }}</router-link>
        </li>
        <li>
          <router-link tag="div" 
          :to="{name: 'adminUser'}"
          :class="{'active': $route.name == 'adminUser'}">{{ $t('admin.users') }}</router-link>
        </li>
        <li>
          <router-link tag="div" 
          :to="{name: 'adminPeriod'}"
          :class="{'active': $route.name == 'adminPeriod'}">{{ $t('admin.periods') }}</router-link>
        </li>
        <li>
          <router-link tag="div" 
          :to="{name: 'adminClass'}"
          :class="{'active': $route.name == 'adminClass'}">{{ $t('admin.class') }}</router-link>
        </li>
        <li>
          <router-link tag="div" 
          :to="{name: 'adminSubject'}"
          :class="{'active': $route.name == 'adminSubject'}">{{ $t('admin.subjects') }}</router-link>
        </li>
        <li>
          <router-link tag="div" 
          :to="{name: 'adminDay'}"
          :class="{'active': $route.name == 'adminDay'}">{{ $t('admin.day') }}</router-link>
        </li>
        <li>
          <router-link tag="div" 
          :to="{name: 'adminClassUser'}"
          :class="{'active': $route.name == 'adminClassUser'}">{{ $t('admin.classUser') }}</router-link>
        </li>
      </ul>
      <button class="button-primary" @click="$router.push({ name: 'masterChoice' })">
        {{ $t('admin.master_choice') }}
      </button>
    </nav>
    <div class="dashboard">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {
    
  },
  data: () => ({
    
  }),
}
</script>

<style lang="scss">

</style>