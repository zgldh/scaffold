<template>
  <span class="notification-button">
    <span class="clickable" @click="click">
    <el-badge :value="unread" :max="99">
      <el-tooltip :disabled="!tooltip" effect="dark" :content="$t('navbar.notification')"
                  placement="bottom">
        <auto-icon icon-class="el-icon-bell"></auto-icon>
      </el-tooltip>
    </el-badge>
    </span>
    <notification-dialog ref="dialog"></notification-dialog>
  </span>
</template>

<script type="javascript">
  import store from '@/store'
  import NotificationDialog from './Dialog.vue'

  export default {
    name: 'notification-button',
    components: {
      NotificationDialog
    },
    props: {
      tooltip: {
        type: Boolean,
        default: true
      }
    },
    data() {
      return {
        loadLatestTimer: null
      }
    },
    computed: {
      unread(){
        return store.state.notification.unread
      }
    },
    mounted(){
      store.dispatch('notification/loadMore')
      this.initTimer();
    },
    beforeDestroy(){
      this.destroyTimer();
    },
    methods: {
      click() {
        this.$refs.dialog.open()
      },
      initTimer(){
        this.loadLatestTimer = setInterval(() => {
          store.dispatch('notification/loadLatest')
        }, 1000 * 60)
      },
      destroyTimer(){
        if (this.loadLatestTimer) {
          clearInterval(this.loadLatestTimer)
        }
      }
    }
  }
</script>

<style lang="scss">
  .notification-button {
    display: inline-block;
    font-size: 20px;
    vertical-align: 13px !important;
    span.clickable {
      cursor: pointer;
    }
    .el-badge {
      vertical-align: top;
    }
    sup.el-badge__content {
      top: 13px;
    }
  }
</style>