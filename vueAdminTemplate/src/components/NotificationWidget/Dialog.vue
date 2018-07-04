<template>
  <el-dialog ref="dialog" class="notification-dialog" :visible.sync="innerVisible">
    <span slot="title">{{$t('navbar.notification')}}</span>
    <div class="dialog-body">
      <div v-if="items.length>0 || hasMore">
        <el-row class="tools">
          <el-col :span="12">{{$t('components.notification.latest')}}</el-col>
          <el-col :span="12" class="tools--right">
            <el-button size="mini" type="text" @click="readAll" v-loading="readingAll">
              {{$t('components.notification.mark_all_as_read')}}
            </el-button>
          </el-col>
        </el-row>

        <el-collapse accordion @change="onSelectedNotificationChange">
          <transition-group name="list">
            <el-collapse-item v-for="item in items" :key="item.id" :name="item.id">
              <notification-title slot="title" :item="item"></notification-title>
              <el-button size="small" type="text" icon="el-icon-d-arrow-right"
                         @click="onNotificationLineClick(item.data.url)">
                {{item.data.url_title}}
              </el-button>
              <div>{{item.data.content}}</div>
            </el-collapse-item>
          </transition-group>
          <infinite-loading @infinite="loadMore">
            <span slot="no-more">{{$t('components.notification.no_more')}}</span>
          </infinite-loading>
        </el-collapse>
      </div>
      <p v-else class="no-notification">
        <auto-icon icon-class="el-icon-success"></auto-icon>
        {{$t('components.notification.no_notification')}}
      </p>
    </div>
  </el-dialog>
</template>

<script type="javascript">
  import store from '@/store'
  import { SuccessMessage } from '@/utils/message'
  import {
    notificationIndex,
    notificationShow,
    notificationDestroy,
    putRead,
    putReadAll
  } from '@/api/notification'
  import NotificationTitle from './NotificationTitle.vue'
  import InfiniteLoading from 'vue-infinite-loading';

  export default {
    name: 'notification-dialog',
    mixins: [],
    components: {
      NotificationTitle,
      InfiniteLoading
    },
    props: {},
    data() {
      return {
        readingAll: false,
        innerVisible: false,
        loading: false
      }
    },
    computed: {
      items(){
        return store.state.notification.items
      },
      page(){
        return store.state.notification.page
      },
      hasMore(){
        return store.state.notification.hasMore
      }
    },
    watch: {},
    mounted(){
    },
    methods: {
      open(){
        this.innerVisible = true;
      },
      async readAll(){
        this.readingAll = true;
        await store.dispatch('notification/readAll');
        this.readingAll = false;
      },
      onNotificationLineClick(url){
        this.innerVisible = false;
        this.$router.push(url);
      },
      async onSelectedNotificationChange(activeNames){
        var notification = this.items.find(item => item.id == activeNames);
        if (notification && !notification.read_at) {
          await store.dispatch('notification/read', notification);
        }
      },
      async loadMore($state){
        this.loading = true;
        await store.dispatch('notification/loadMore');
        $state.loaded();
        if (!this.hasMore) {
          $state.complete();
        }
        this.loading = false;
      }
    }
  }
</script>
<style rel="stylesheet/scss" lang="scss">
  @import "../../styles/variables";

  .notification-dialog {
    .el-dialog {
      overflow: hidden;
      display: flex;
      flex-direction: column;
      height: 70vh;

      .el-dialog__body {
        height: calc(100% - 75px);
        padding-top: 0;
        .dialog-body {
          height: 100%;
          & > div {
            height: 100%;
            .el-collapse {
              height: calc(100% - 20px);
              overflow-y: scroll;
              overflow-x: hidden;
            }
          }
        }
      }
    }
    .tools--right {
      text-align: right;
    }
    .no-notification {
      text-align: center;
      color: $success;
    }

    .list-enter-active, .list-leave-active {
      transition: all 1s;
    }
    .list-enter, .list-leave-to /* .list-leave-active below version 2.1.8 */
    {
      opacity: 0;
      transform: translateY(30px);
    }
  }
</style>
