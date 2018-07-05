<template>
  <div class="permissions-matrix-model-row">
    <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll"
                 @change="handleCheckAllChange">{{modelName}}
    </el-checkbox>
    <hr>
    <el-checkbox-group v-model="checkedPermissionIds"
                       @change="handleCheckedPermissionsChange">
      <el-checkbox v-for="permission in permissions" :label="permission.id"
                   :key="permission.id">
        <permission-label :permission-name="permission.name"></permission-label>
      </el-checkbox>
    </el-checkbox-group>
  </div>
</template>

<script type="javascript">
  import store  from '@/store'
  import { mapState } from 'vuex'
  import PermissionLabel  from '@/components/Permission/PermissionLabel'
  import { SortPermission, FindPermissionIndex } from '@/utils/permission'

  export default {
    name: 'permissions-matrix-model-row',
    components: { PermissionLabel },
    props: {
      value: Array, // Checked ids
      permissionModel: {}
    },
    data() {
      return {
        checkAll: false,
        isIndeterminate: false,
        checkedPermissionIds: [],
      }
    },
    computed: {
      modelName(){
        var modelName = _.snakeCase(_.get(this.permissionModel, 'modelName'));
        if (modelName) {
          return this.$t(modelName + '.title');
        }
        else {
          return this.$t('components.permission_matrix.model_row.other');
        }
      },
      permissions(){
        if (!this.permissionModel.hasOwnProperty('permissions')) {
          return [];
        }
        return SortPermission(this.permissionModel.permissions);
      }
    },
    mounted(){
      this.checkedPermissionIds = this.permissionModel.checkedPermissionIds;
    },
    watch: {
      permissionModel: {
        handler: function (newValue) {
          this.checkedPermissionIds = newValue.checkedPermissionIds;
          let checkedCount = this.checkedPermissionIds.length;
          this.checkAll = checkedCount === this.permissions.length;
          this.isIndeterminate = checkedCount > 0 && checkedCount < this.permissions.length;
        },
        deep: true
      }
    },
    methods: {
      getCellContent(row, actionName){
        var permission = row.permissions.find(item => item.name.includes('@' + actionName));
        if (permission) {
          return permission.name;
        }
        return '';
      },
      handleCheckAllChange(val) {
        this.checkedPermissionIds = val ? this.permissions.map(item => item.id) : [];
        this.isIndeterminate = false;
        this.emitChange();
      },
      handleCheckedPermissionsChange(value) {
        let checkedCount = value.length;
        this.checkAll = checkedCount === this.permissions.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.permissions.length;
        this.emitChange();
      },
      emitChange(){
        this.$emit('changed',
                {
                  modelName: _.get(this.permissionModel, 'modelName'),
                  checkedPermissionIds: this.checkedPermissionIds
                });
      }
    }
  }
</script>
<style rel="stylesheet/scss" lang="scss">
  @import "../../../styles/variables.scss";

  .permissions-matrix-model-row {
    margin: 15px 0;
    padding: 8px 16px;
    background-color: $borderL4;
    border-radius: 4px;
    border-left: 5px solid $borderL1;
    .el-checkbox {
      margin: 0 15px 5px 0 !important;
    }
    hr {
      border-bottom: none;
    }
  }
</style>

