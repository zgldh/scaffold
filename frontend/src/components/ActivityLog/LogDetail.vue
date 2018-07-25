<template>
    <component class="log-detail" v-bind:is="logAction" :log="log"></component>
</template>

<script type="javascript">
  import _ from 'lodash'

  export default {
    name: 'log-detail',
    components: {
      'Login': () => import('./Actions/Login.vue'),
      'Logout': () => import('./Actions/Logout.vue'),
      'Created': () => import('./Actions/Created.vue'),
      'Updated': () => import('./Actions/Updated.vue'),
      'Deleted': () => import('./Actions/Deleted.vue')
    },
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
      logAction() {
        return _.upperFirst(this.log.description)
      },
      action() {
        if (this.log.description) {
          return this.$i18n.t('activity_log.type.' + this.log.description)
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

    .log-detail {
    }
</style>

