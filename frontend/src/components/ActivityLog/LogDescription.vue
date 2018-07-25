<template>
    <div class="log-description">
        <el-tag class="causer" v-if="log.causer_id" size="mini">{{causer}}</el-tag>
        <span class="action" v-if="log.description">{{action}}</span>
        <el-tag class="subject" v-if="log.subject_id" size="mini">{{subject}}</el-tag>
    </div>
</template>

<script type="javascript">
  import _ from 'lodash'

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
          var modelName = _.last(this.log.subject_type.split('\\')).toLowerCase()
          return this.$t(modelName + '.title') + ' (' + this.log.subject_id + ')'
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

