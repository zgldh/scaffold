<template>
  <div class="notification-title" :class="{unread:!item.read_at}">
    <notifier :notifier="item.data.notifier"/>
    <span>{{type}}</span>
    <div class="tools pull-right">
      <span class="created-at" :title="item.created_at">{{createdAt}}</span>
      <el-tooltip effect="dark" :content="$t('global.terms.delete')" placement="right"
                  :enterable="false">
        <el-button class="delete-button" type="text" :circle="true" v-loading="deleting"
                   icon="el-icon-close" @click.stop="remove"></el-button>
      </el-tooltip>
      <el-tooltip effect="dark"
                  :content="item.read_at?$t('components.notification.mark_as_unread'):$t('components.notification.mark_as_read')"
                  placement="right"
                  :enterable="false">
        <el-button class="read-button" type="text" :circle="true" icon="el-icon-warning"
                   v-loading="reading"
                   @click.stop="toggleRead"></el-button>
      </el-tooltip>
    </div>
  </div>
</template>

<script type="javascript">
  import store from '@/store'
  import Notifier from './Notifier.vue'
  import moment from 'moment'

  export default {
    name: 'notification-title',
    mixins: [],
    components: {
      Notifier
    },
    props: {
      item: {
        type: Object,
        required: true
      }
    },
    data() {
      return {
        reading: false,
        deleting: false,
        createdAt: ''
      }
    },
    computed: {
      type(){
        return this.item.type;
      },
      notifier(){
        return this.item.data.notifier;
      },
      language(){
        return store.state.app.language;
      }
    },
    watch: {
      item: {
        handler: function () {
          this.reRender()
        },
        deep: true
      },
      language(){
        this.reRender();
      }
    },
    mounted(){
      this.reRender();
    },
    methods: {
      reRender(){
        if (this.item) {
          this.createdAt = moment(this.item.created_at).from();
        }
      },
      async toggleRead(){
        this.reading = true;
        const actionName = this.item.read_at ? 'notification/unread' : 'notification/read';
        await store.dispatch(actionName, this.item);
        this.reading = false;
      },
      async remove(){
        this.deleting = true;
        await store.dispatch('notification/remove', this.item);
        this.deleting = false;
      }
    }
  }
</script>
<style rel="stylesheet/scss" lang="scss">
  @import "../../styles/variables";

  .notification-title {
    .read-button {
      i {
        color: $borderL3;
      }
    }
    &.unread {
      font-weight: bolder;
      .read-button {
        i {
          color: inherit;
        }
      }
    }
    .tools {
      margin-right: 1em;
    }
    .created-at {

    }
    .delete-button {
      display: none;
    }
    &:hover {
      .delete-button {
        display: inline-block;
      }
    }
  }
</style>
