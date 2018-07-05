<template>
  <div class="permissions-matrix">
    <model-row v-for="model in models" :key="model.modelName"
               @changed="onChanged"
               :permission-model="model"
    ></model-row>
  </div>
</template>

<script type="javascript">
  import store  from '@/store'
  import { mapState } from 'vuex'
  import ModelRow from './ModelRow.vue'

  function FindOrCreateModelByPermission(models, permission) {
    var modelName = GetModelName(permission);
    var model = models.find(item => item.modelName === modelName)
    if (!model) {
      model = {
        modelName: modelName,
        permissions: [],
        checkedPermissionIds: [],
      };
      if (modelName) {
        models.unshift(model);
      }
      else {
        models.push(model);
      }
    }
    return model;
  }

  function GetModelName(permission) {
    if (permission.constructor !== String) {
      permission = permission.name;
    }
    var modelName = null;
    if (permission.indexOf('@') >= 0) {
      modelName = permission.split('@', 2)[0];
    }
    return modelName;
  }

  export default {
    name: 'permissions-matrix',
    components: {
      ModelRow
    },
    props: {
      value: Array  // Current user permissions
    },
    data() {
      return {
        models: [],
      }
    },
    computed: {
      ...mapState({
        permissions: state => state.user.permissions
      })
    },
    beforeCreate(to, from, next){
      store.dispatch('user/LoadPermissions').then(() => {
        this.rebuildModels();
      });
    },
    watch: {
      value: function (newValue) {
        this.applyCheckedPermissions(newValue);
      },
      checked: {
        handler: function (newValue) {
          this.sendPutRequest();
        },
        deep: true
      }
    },
    methods: {
      rebuildModels(){
        var models = [];
        this.permissions.forEach(permission => {
          var model = FindOrCreateModelByPermission(models, permission);
          model.permissions.push(permission);
        });
        this.models = models;
      },
      applyCheckedPermissions(checkedPermissions){
        this.models.forEach(model => {
          model.checkedPermissionIds = [];
        });
        checkedPermissions.forEach(checkedPermission => {
          let model = FindOrCreateModelByPermission(this.models, checkedPermission);
          model.checkedPermissionIds.push(checkedPermission.id);
        });
      },
      onChanged({
                  modelName,
                  checkedPermissionIds
                }){
        var model = this.models.find(item => item.modelName === modelName);
        if (model) {
          model.checkedPermissionIds = checkedPermissionIds;
        }
        var permissionIds = this.models.reduce((ids, model) => {
          ids.push.apply(ids, model.checkedPermissionIds);
          return ids;
        }, []);
        var permissions = permissionIds.map(id => this.permissions.find(permission => permission.id === id));
        this.$emit('input', permissions);
      }
    }
  }
</script>
<style rel="stylesheet/scss" lang="scss">
  @import "../../../styles/variables.scss";

  .permissions-matrix {
    width: 100%;
    .permission-module {
      margin: 15px 0;
      padding: 8px 16px;
      background-color: $borderL4;
      border-radius: 4px;
      border-left: 5px solid $borderL1;
      .el-checkbox {
        margin: 0 15px 5px 0 !important;
      }
    }
  }
</style>

