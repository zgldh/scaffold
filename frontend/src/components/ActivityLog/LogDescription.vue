<template>
    <div class="log-description">
        <el-tag class="causer" v-if="log.causer_id" size="mini">{{causer}}</el-tag>
        <span class="action" v-if="log.description">{{action}}</span>
        <span class="subject" v-if="log.subject_id">{{subject}}</span>
    </div>
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
    name: 'log-description',
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
    @import "../../styles/variables.scss";

    .log-description {
    }
</style>

