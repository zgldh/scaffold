<template>
  <span class="permissoin-label" :title="permissionName">{{renderLabel}}</span>
</template>

<script type="javascript">
  import { PermissionLang } from '@/utils/permission'

  export default {
    name: 'permission-label',
    props: {
      permissionName: String,
      label: String
    },
    data() {
      return {}
    },
    computed: {
      renderLabel(){
        return this.label || PermissionLang(this.permissionName);
      }
    },
    methods: {}
  }
</script>
<style rel="stylesheet/scss" lang="scss">
  .permission-label {
  }
</style>

