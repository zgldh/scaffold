<template>
  <div class="permission-list-page bga-back-topyarn add vue-multiple-back-top  --save"
       v-loading="loading">
    <el-row type="flex" justify="space-between">
      <el-col :span="12">
        <list-title :name="$t('permission.title')"></list-title>
      </el-col>
      <el-col :span="6">
        <el-button class="add-button" size="small" @click="onCreate">
          {{$t('global.terms.create')}}
        </el-button>
      </el-col>
      <el-col :span="6">
        <el-input class="filter" size="small"
                  :placeholder="$t('global.terms.auto_search')"
                  v-model="filter"
                  @change="onFilterChange"
                  clearable>
        </el-input>
      </el-col>
    </el-row>
    <el-row>
      <el-col :span="24">
        <div class="permissions">
          <permission-row v-for="(permission, $index) in permissions"
                          :key="permission.id" :permission="permission"
                          :permissions="permissions"
                          :selected-roles="selectedRoles[permission.id]"
                          @edit="onEdit"
                          @delete="onDelete"
                          @changed="onChanged"
                          v-loading="loadingId===permission.id"></permission-row>
        </div>
      </el-col>
    </el-row>
    <permission-editor-dialog ref="editor" :models="models"></permission-editor-dialog>
    <scroll-to-top right="150px"></scroll-to-top>
  </div>
</template>

<script type="javascript">
  import PermissionRow from '@/components/Permission/PermissionRow'
  import PermissionEditorDialog  from '@/components/Permission/PermissionEditorDialog'
  import ScrollToTop from '@/components/ScrollToTop'
  import ListMixin from '@/mixins/List'
  import store from '@/store'
  import { RoleIndex, PermissionSyncRoles } from '@/api/user'
  import { SuccessMessage, DeleteConfirm } from '@/utils/message'
  import {
    SortPermission,
    PermissionFullLang,
    SplitModelAction,
    ModelLang
  } from '@/utils/permission'

  var selectedRoles = [];

  export default {
    components: { ScrollToTop, PermissionRow, PermissionEditorDialog },
    mixins: [ListMixin],
    computed: {
      permissions(){
        var result = SortPermission(this.$store.state.user.permissions);
        if (this.innerFilter) {
          return result.filter(item => PermissionFullLang(item.name).toLowerCase().indexOf(this.innerFilter) >= 0);
        }
        return result;
      },
      models(){
        var models = {};
        this.permissions.forEach((item) => {
          var model, action;
          [model, action] = SplitModelAction(item.name)
          if (models.hasOwnProperty(model) === false) {
            models[model] = ModelLang(_.snakeCase(model))
          }
        });
        return models;
      }
    },
    data (){
      return {
        filter: '',
        innerFilter: '',
        roles: [],
        selectedRoles: [],
        items: [],
        loading: false,
        loadingId: null,
      };
    },
    beforeRouteEnter(to, from, next){
      store.dispatch('user/LoadPermissions')
              .then(store.dispatch('user/LoadRoles'))
              .then(next);
    },
    mounted(){
      this.fetchData();
    },
    methods: {
      onFilterChange: _.debounce(function () {
        this.innerFilter = this.filter.toLowerCase();
      }, 500),
      fetchData() {
        this.loading = true;
        RoleIndex({ _with: 'permissions' })
                .then(res => this.roles = res.data)
                .then(res => this.rebuildSelectedRoles())
                .then(res => this.loading = false)
      },
      onCreate(){
        // this.loadingId = permission.id;
        this.$refs.editor.create().then(result => store.commit('user/ADD_PERMISSION', result))
      },
      onEdit({ permission, roles }){
        // this.loadingId = permission.id;
        this.$refs.editor.show(permission).then(result => store.commit('user/SET_PERMISSION', result))
      },
      onDelete({ permission, roles }){
        DeleteConfirm(PermissionFullLang(permission.name), async () => {
          this.loadingId = permission.id
          try {
            await store.dispatch('user/removePermission', permission.id)
          } finally {
            this.loadingId = null
          }
        });
      },
      async onChanged({ permission, roles }){
        this.loadingId = permission.id;
        try {
          var result = await PermissionSyncRoles(permission.id, { roleIds: roles.map(role => role.id) })
        } catch (e) {
          this.selectedRoles[permission.id] = this.selectedRoles[permission.id].slice(0)
        } finally {
          this.loadingId = null
        }
      },
      findRolesByPermission(permission){
        return this.roles.filter(role => role.permissions.findIndex(perm => perm.id === permission.id) >= 0)
      },
      rebuildSelectedRoles(){
        this.selectedRoles = [];
        this.permissions.forEach((permission, index) => {
          this.selectedRoles[permission.id] = this.findRolesByPermission(permission);
        });
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  .permission-list-page {
    margin: 10px 30px;

    .add-button {
      margin: 1em .5em;
      float: right;
    }
    .filter {
      line-height: 58px;
    }
  }
</style>
