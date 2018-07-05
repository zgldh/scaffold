<template>
  <span class="role-label">{{label}}</span>
</template>

<script type="javascript">
  import store  from '@/store'
  import { mapState } from 'vuex'
  import { SplitModelAction } from '@/utils/permission'

  export default {
    name: 'role-label',
    props: {
      roleName: String
    },
    data() {
      return {}
    },
    computed: {
      label(){
        var roleName = _.snakeCase(this.roleName);
        return this.$t('role.roles.' + roleName);
      }
    },
    methods: {}
  }
</script>
<style rel="stylesheet/scss" lang="scss">
  .role-label {
  }
</style>

