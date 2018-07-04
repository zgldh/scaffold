<template>
  <div class="dashboard-container">
    <div class="dashboard-text">name:{{name}}</div>
    <div class="dashboard-text">roles:<span v-for='role in roles'
                                            :key='role'>{{role}}</span></div>
  </div>
</template>

<script type="javascript">
  import { mapState } from 'vuex'

  export default {
    name: 'dashboard',
    computed: {
      ...mapState({
        name: state => state.currentUser.name,
        roles: state => state.currentUser.roles,
      })
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .dashboard {
    &-container {
      margin: 10px 30px;
    }
    &-text {
      font-size: 30px;
      line-height: 46px;
    }
  }
</style>
