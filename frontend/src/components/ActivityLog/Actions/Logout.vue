<template>
    <div class="log-action-logout"></div>
</template>

<script type="javascript">
  import store from '@/store'
  import { mapState } from 'vuex'
  import CellAction from '@/components/CellActions'
  import PermissionLabel from '@/components/Permission/PermissionLabel'
  import RoleLabel from '@/components/Permission/RoleLabel'
  import { ModelLang } from '@/utils/permission'
  import {
    SuccessMessage,
    TextCopyDialog,
  } from '@/utils/message'

  export default {
    name: 'log-action-logout',
    components: {},
    props: {
      log: {
        type: Object,
        required: true
      }
    },
    data() {
      return {}
    },
    computed: {
      causer() {
        if (this.log.causer) {
          return this.log.causer.name
        }
        return null
      },
      action() {
        if (this.log.description) {
          return this.$i18n.t('activity_log.type.' + this.log.description)
        }
        return null
      },
      subject() {
        if (this.log.subject_id) {
          return this.log.subject_id
        }
        return null
      }
    },
    mounted() {
    },
    watch: {},
    methods: {}
  }
</script>
<style rel="stylesheet/scss" lang="scss">
    @import "../../../styles/variables.scss";

    .log-action-logout {
    }
</style>

