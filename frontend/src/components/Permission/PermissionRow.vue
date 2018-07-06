<template>
  <div class="permission-row" :model-name="'model-'+modelName"
       :class="{last:isModelLast}">
    <el-row justify="start" type="flex">
      <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll"
                   @change="handleCheckAllChange">{{modelNameI18N}} : {{permission.label}}
      </el-checkbox>
      <el-tooltip placement="right" :content="$t('global.terms.copy')">
        <el-button class="copy-button" size="small" plain @click="copyPermissionName">
          {{permission.name}}
        </el-button>
      </el-tooltip>
      <span class="flex"></span>
      <cell-action :target="permission" :actions="actions"></cell-action>
    </el-row>
    <el-checkbox-group v-model="checkedRoleIds"
                       @change="handleCheckedPermissionsChange">
      <el-checkbox v-for="role in roles" :label="role.id"
                   :key="role.id">
        <role-label :role-name="role.name"></role-label>
      </el-checkbox>
    </el-checkbox-group>
  </div>
</template>

<script type="javascript">
  import store  from '@/store'
  import { mapState } from 'vuex'
  import CellAction  from '@/components/CellActions'
  import PermissionLabel  from '@/components/Permission/PermissionLabel'
  import RoleLabel  from '@/components/Permission/RoleLabel'
  import { ModelLang } from '@/utils/permission'
  import {
    SuccessMessage,
    TextCopyDialog,
  } from '@/utils/message'

  export default {
    name: 'permission-row',
    components: { PermissionLabel, RoleLabel, CellAction },
    props: {
      permission: Object,
      selectedRoles: Array,
      permissions: {
        type: Array,
        default: () => {
          return []
        },
        required: false
      }
    },
    data() {
      return {
        checkAll: false,
        isIndeterminate: false,
        checkedRoleIds: [],
        isModelLast: false,
        actions: [
          {
            Title: () => this.$i18n.t('global.terms.edit'),
            Handle: this.handleEdit,
            IsVisible: () => store.getters.hasPermission('Permission@edit')
          },
          {
            Title: () => this.$i18n.t('global.terms.delete'),
            Handle: this.handleDelete,
            IsVisible: () => store.getters.hasPermission('Permission@destroy')
          },
        ]
      }
    },
    computed: {
      ...mapState({
        roles: state => state.user.roles
      }),
      modelName(){
        var permissionName = _.get(this.permission, 'name');
        var model, action;
        [model, action] = permissionName.split('@');
        var modelName = _.snakeCase(model);
        return modelName;
      },
      modelNameI18N(){
        return ModelLang(this.modelName);
      },
    },
    mounted(){
      this.checkIsModelLast();
      this.checkedRoleIds = this.selectedRoles ? this.selectedRoles.map(role => role.id) : [];
      let checkedCount = this.checkedRoleIds.length;
      this.checkAll = checkedCount === this.roles.length;
      this.isIndeterminate = checkedCount > 0 && checkedCount < this.roles.length;
    },
    watch: {
      selectedRoles: {
        handler: function (newValue) {
          this.checkedRoleIds = newValue ? newValue.map(role => role.id) : [];

          let checkedCount = this.checkedRoleIds.length;
          this.checkAll = checkedCount === this.roles.length;
          this.isIndeterminate = checkedCount > 0 && checkedCount < this.roles.length;
        },
        deep: true
      },
      permission(){
        this.checkIsModelLast();
      },
      permissions(){
        this.checkIsModelLast(true);
      }
    },
    methods: {
      handleEdit(item){
        this.$emit('edit',
                {
                  permission: { ...this.permission },
                  roles: this.checkedRoleIds.map(id => this.roles.find(role => role.id === id))
                });
      },
      handleDelete(item){
        this.$emit('delete',
                {
                  permission: { ...this.permission },
                  roles: this.checkedRoleIds.map(id => this.roles.find(role => role.id === id))
                });
      },
      handleCheckAllChange(value){
        this.checkedRoleIds = value ? this.roles.map(item => item.id) : [];
        this.isIndeterminate = false;
        this.emitChange();
      },
      handleCheckedPermissionsChange(value){
        let checkedCount = value.length;
        this.checkAll = checkedCount === this.roles.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.roles.length;
        this.emitChange();
      },
      emitChange(){
        this.$emit('changed',
                {
                  permission: this.permission,
                  roles: this.checkedRoleIds.map(id => this.roles.find(role => role.id === id))
                });
      },
      checkIsModelLast(selfOnly){
        this.$nextTick(() => {
          if (this.$el.previousSibling && selfOnly !== true) {
            this.$el.previousSibling.__vue__.checkIsModelLast();
          }
          if (this.$el.nextSibling) {
            this.isModelLast = this.$el.getAttribute('model-name') !== this.$el.nextSibling.getAttribute('model-name');
          }
          else {
            this.isModelLast = true;
          }
        })
      },
      copyPermissionName(){
        this.$copyText(this.permission.name).then(SuccessMessage(this.$t('messages.text_copy.complete')), (e) => {
          return TextCopyDialog(message)
        })
      }
    }
  }
</script>
<style rel="stylesheet/scss" lang="scss">
  @import "../../styles/variables.scss";

  .permission-row {
    margin: 0;
    padding: 8px 16px;
    background-color: $borderL4;
    border-left: 5px solid $borderL1;
    border-bottom: 1px solid $borderL1;
    -webkit-transition: background-color .2s, border-left-color .2s;
    -moz-transition: background-color .2s, border-left-color .2s;
    -ms-transition: background-color .2s, border-left-color .2s;
    -o-transition: background-color .2s, border-left-color .2s;
    transition: background-color .2s, border-left-color .2s;

    &:first-child {
      border-radius: 4px 4px 0 0;
    }
    &.last {
      border-bottom: none;
      margin-bottom: 15px;
      border-radius: 0 0 4px 4px;
      & + .permission-row {
        border-radius: 4px 4px 0 0;
      }
    }
    &:hover {
      background-color: white;
      border-left-color: adjust_color($borderL1, $lightness: -50%);
    }

    .copy-button {
      padding: 2px 8px;
      height: 22px;
    }

    .cell-actions {
      display: inline-block;
    }

    .el-checkbox {
      margin: 0 15px 5px 0 !important;
    }
    hr {
      border-bottom: none;
    }
  }
</style>

