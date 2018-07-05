<template>
  <div class="cell-actions">
    <el-button v-for="(action, $index) in palpableActions" :key="$index"
               @click="handleClick(action)"
               :disabled="isDisabled(action)"
               :type="type" :size="size" :title="getActionTitle(action.Tooltip)">
      <auto-icon v-if="action.Icon" :icon-class="action.Icon"></auto-icon>
      <span v-if="action.Title">{{getActionTitle(action.Title)}}</span>
    </el-button>

    <el-dropdown v-if="moreActions.length>0" @command="handleMoreAction" :size="size">
      <el-button :type="type" :size="size">
        {{$t('global.terms.more')}}<i class="el-icon-arrow-down el-icon--right"></i>
      </el-button>
      <el-dropdown-menu slot="dropdown">
        <el-dropdown-item v-for="(action, $index) in moreActions" :key="$index"
                          :disabled="isDisabled(action)"
                          :command="action" :title="getActionTitle(action.Tooltip)">
          <auto-icon v-if="action.Icon" :icon-class="action.Icon"></auto-icon>
          <span v-if="action.Title">{{getActionTitle(action.Title)}}</span>
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>

  </div>
</template>

<script type="javascript">
  import AutoIcon from '@/components/AutoIcon'

  export default {
    name: 'cell-actions',
    components: {
      AutoIcon
    },
    props: {
      target: {
        default: null
      },
      type: {
        type: String,
        default: 'text'
      },
      size: {
        type: String,
        default: 'small'
      },
      /**
       *  [
       *    {
       *      Title: 'Edit',
       *      Title: () => this.$i18n.t('global.terms.create'), // Prefer to use for i18n
       *      Tooltip: 'Edit',
       *      Tooltip: () => this.$i18n.t('global.terms.create'), // Prefer to use for i18n
       *      Icon: 'fa fa-edit',
       *      Handle(target){
       *        console.log(target);
       *      },
       *      More: false // true to more into [More...]
       *      TargetCare: false // Set to true, the action will be enabled ONLY when target is available
       *      IsVisible: true|false,
       *      IsVisible: (target)=>{ return true } // Determin the action show up or not.
       *    }
       *  ]
       **/
      actions: {
        type: Array,
        default(){
          return [];
        }
      }
    },
    data(){
      return {}
    },
    mounted(){
    },
    computed: {
      palpableActions(){
        return this.actions.filter(action => !action.More).filter(this.isVisible);
      },
      moreActions(){
        return this.actions.filter(action => action.More).filter(this.isVisible);
      }
    },
    watch: {},
    methods: {
      handleClick(action){
        if (action.hasOwnProperty('Handle')) {
          action.Handle(this.target);
        }
      },
      handleMoreAction(action){
        if (action.hasOwnProperty('Handle')) {
          action.Handle(this.target);
        }
      },
      getActionTitle(title){
        if (title && title.constructor === Function) {
          return title();
        }
        return title;
      },
      isDisabled(action){
        if (action.TargetCare) {
          if (!this.target) {
            return true;
          }
          if (this.target.constructor === Array) {
            if (this.target.length === 0) {
              return true;
            }
          }
        }
        return false;
      },
      isVisible(action) {
        if(action.hasOwnProperty('IsVisible'))
        {
          if(action.IsVisible.constructor === Boolean)
          {
            return action.IsVisible;
          }
          else if(action.IsVisible.constructor === Function)
          {
            return action.IsVisible(this.target);
          }
          return false;
        }
        else{
          return true;
        }
      }
    }
  }
</script>

<style lang="scss">
  .cell-actions {
    .el-dropdown {
      margin-left: 10px;
    }
  }
</style>
