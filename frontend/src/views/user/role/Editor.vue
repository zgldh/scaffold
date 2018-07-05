<template>
  <div class="role-editor-page" v-loading="loading">
    <el-row>
      <el-col :span="24">
        <editor-title :name="$t('role.title')"></editor-title>

        <el-form label-position="right" label-width="80px" :rules="rules" :model="form"
                 ref="form">
          <form-item prop="name" :label="$t('role.fields.name')" :required="true">
            <el-input v-model="form.name"></el-input>
          </form-item>
          <form-item prop="label" :label="$t('role.fields.label')" :required="true">
            <el-input v-model="form.label"></el-input>
          </form-item>
          <form-item :label="$t('permission.title')">
            <permissions-matrix v-model="form.permissions"></permissions-matrix>
          </form-item>
          <form-item>
            <el-button type="primary" @click="isCreating?onCreate():onUpdate()">
              {{$t('global.terms.submit')}}
            </el-button>
            <el-button @click="onCopy" v-if="!isCreating">{{$t('global.terms.copy')}}
            </el-button>
            <el-button @click="$router.go(-1)">{{$t('global.terms.back')}}</el-button>
          </form-item>
        </el-form>
      </el-col>
      <el-col :span="11" :offset="1">
      </el-col>
    </el-row>
  </div>
</template>

<script type="javascript">
  import store from '@/store'
  import { mapState } from 'vuex'
  import PermissionsMatrix from '@/components/Permission/PermissionsMatrix'
  import { SuccessMessage, DeleteConfirm, Error422 } from '@/utils/message'
  import { PermissionIndex, RoleShow, RoleStore, RoleUpdate, RoleCopy } from '@/api/user'
  import EditorMixin from '@/mixins/Editor'
  import { RoleCopyDialog } from '@/utils/permission'

  export default {
    components: {
      PermissionsMatrix
    },
    mixins: [EditorMixin],
    data() {
      return {
        rules: {
          email: {
            required: true,
            type: 'email',
            trigger: ['blur', 'change']
          },
        },
        form: {
          name: '',
          label: '',
          guard_name: 'api',
          permissions: []
        },
      };
    },
    computed: {
      ...mapState({
        permissions: state => state.user.permissions,
        language: state => state.app.language,
      })
    },
    beforeRouteEnter(to, from, next){
      store.dispatch('user/LoadPermissions').then(next);
    },
    beforeRouteUpdate(to, from, next){
      this.$el.parentElement.scrollTop = 0
      next()
    },
    mounted() {
      this.fetchData()
    },
    watch: {
      $route: 'fetchData',
      language(){
        this.fetchData();
      }
    },
    methods: {
      fetchData() {
        if (this.$route.params.id) {
          this.loading = true;
          RoleShow(this.$route.params.id + '?_with=permissions')
                  .then(res => this.form = res.data)
                  .then(res => this.loading = false)
                  .catch(err => this.loading = false)
        }
      },
      onCreate(){
        this.$refs.form.validate().then(valid => {
          this.loading = true;
          return RoleStore(this.form, '_with=permissions');
        })
                .then(res => this.form = res.data)
                .then(SuccessMessage(this.$t('global.terms.save_completed')))
                .then(res => {
                  this.loading = false;
                  this.$router.replace({ path: `/user/role/${this.form.id}/edit` });
                })
                .catch(this.errorHandler);
      },
      onUpdate(){
        this.$refs.form.validate().then(valid => {
          this.loading = true;
          return RoleUpdate(this.form.id, '_with=permissions', this.form);
        })
                .then(res => this.form = res.data)
                .then(SuccessMessage(this.$t('global.terms.save_completed')))
                .then(res => this.loading = false)
                .catch(this.errorHandler);
      },
      onCopy(){
        var newRole = null;
        RoleCopyDialog(this.form).then(({ value }) => {
          this.loading = true
          return RoleCopy({
            id: this.form.id,
            name: value
          })
        }).then(result => {
          this.loading = false
          newRole = result.data;
          return this.$confirm(this.$t('pages.role.terms.copy_complete_text'), {
            confirmButtonText: this.$t('global.terms.confirm'),
            cancelButtonText: this.$t('global.terms.cancel'),
            type: 'success'
          })
        }).then(() => {
          this.$router.replace({ path: `/user/role/${newRole.id}/edit` });
        }).catch(err => {
          this.loading = false
          Error422(err)
        });
      }
    }
  };
</script>

<style rel="stylesheet/scss" lang="scss">
  .role-editor-page {
    margin: 10px 30px;
  }
</style>
