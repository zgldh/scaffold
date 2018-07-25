<template>
    <div class="log-action-created"></div>
</template>

<script type="javascript">

  export default {
    name: 'log-action-created',
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

    .log-action-created {
    }
</style>

