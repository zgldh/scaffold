<template>
  <div class="admin-editor-page">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{$t('user.title')}}
        <small v-if="form.id">{{$t('global.terms.edit')}}</small>
        <small v-else>{{$t('global.terms.create')}}</small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="box box-default">

        <div class="box-header with-border">
          <el-button type="default" @click="onCancel" icon="close">
            {{$t('global.terms.back')}}
          </el-button>
          <el-button type="primary" @click="onSave" icon="check"
                     :loading="saving||loading">
            {{$t('global.terms.save')}}
          </el-button>
        </div>
        <!-- /.box-header -->

        <!-- form start -->
        <div class="box-body">
          <el-alert class="missing-errors" v-if="missingErrors.length"
                    v-for="errorMessage in missingErrors"
                    :key="errorMessage"
                    :title="errorMessage" type="error" show-icon></el-alert>

          <!-- form start -->
          <el-form ref="form" :model="form" label-width="200px" v-loading="loading">
            <el-form-item :label="$t('global.fields.id')" v-if="form.id">
              <el-input v-model="form.id" disabled></el-input>
            </el-form-item>
            <el-form-item :label="$t('user.fields.name')" prop="name"
                          :error="errors.name">
              <el-input v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item :label="$t('user.fields.email')" prop="email"
                          :error="errors.email">
              <el-input v-model="form.email"></el-input>
            </el-form-item>
            <el-form-item :label="$t('user.fields.password')"
                          prop="password"
                          :error="errors.password">
              <el-input v-model="form.password" type="password"></el-input>
              <p class="assist-tip" v-if="form.id">
                {{$t('module_user.terms.leave_field_blank_if_dont_want_change_password')}}</p>
            </el-form-item>
            <el-form-item :label="$t('user.fields.gender')"
                          prop="gender" :error="errors.gender">
              <el-radio-group v-model="form.gender">
                <el-radio label="male">男 Male</el-radio>
                <el-radio label="female">女 Female</el-radio>
              </el-radio-group>
            </el-form-item>

            <el-form-item :label="$t('user.fields.avatar')"
                          prop="avatar" :error="errors.avatar">
              <upload-component v-model="form.avatar" list-type="picture-card"
                                accept="image/*"></upload-component>
            </el-form-item>

            <el-form-item :label="$t('user.fields.mobile')"
                          prop="mobile" :error="errors.mobile">
              <el-input v-model="form.mobile"></el-input>
            </el-form-item>
            <el-form-item :label="$t('user.fields.is_active')"
                          prop="is_active"
                          :error="errors.is_active">
              <el-radio-group v-model="form.is_active">
                <el-radio :label="1">Active</el-radio>
                <el-radio :label="0">Inactive</el-radio>
              </el-radio-group>
            </el-form-item>

            <el-form-item :label="$t('user.fields.roles')" prop="roles"
                          :error="errors.roles">
              <el-select class="role-selector" v-model="form.roles" filterable multiple>
                <el-option
                        v-for="role in roles"
                        :key="role.id"
                        :label="role.label"
                        :value="role.id">
                </el-option>
              </el-select>
            </el-form-item>

            <el-form-item :label="$t('user.fields.permissions')"
                          prop="permissions"
                          :error="errors.permissions">
              <el-select class="permission-selector" v-model="form.permissions" filterable
                         multiple>
                <el-option
                        v-for="permission in permissions"
                        :key="permission.id"
                        :label="permission.label"
                        :value="permission.id">
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item :label="$t('user.fields.last_login_at')"
                          v-if="form.id">
              <el-input v-model="form.last_login_at" disabled></el-input>
            </el-form-item>
            <el-form-item :label="$t('user.fields.login_times')"
                          v-if="form.id">
              <el-input v-model="form.login_times" disabled></el-input>
            </el-form-item>
          </el-form>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
          <el-button type="default" @click="onCancel" icon="close">
            {{$t('global.terms.back')}}
          </el-button>
          <el-button type="primary" @click="onSave" icon="check"
                     :loading="saving||loading">
            {{$t('global.terms.save')}}
          </el-button>
        </div>

      </div>

    </section>
    <!-- /.content -->
  </div>
</template>

<script type="text/javascript">
  import { mixin } from "resources/assets/js/commons/EditorHelper.js";
  import { loadModuleLanguage } from 'resources/assets/js/commons/LanguageHelper';
  import UploadComponent from 'Modules/Upload/resources/assets/Components/Upload.vue';

  export default  {
    mixins: [
      mixin,
      loadModuleLanguage('module_user')
    ],
    data: function () {
      return {
        form: {
          id: null,
          name: '',
          email: '',
          password: '',
          gender: 'male',
          mobile: '',
          is_active: 1,
          last_login_at: null,
          login_times: 0,
          avatar: null,
          roles: [],
          permissions: []
        },
        roles: [],
        permissions: []
      };
    },
    components: {
      UploadComponent
    },
    computed: {
      resource: function () {
        var resourceURL = '/user';
        return (this.form.id ? resourceURL + '/' + this.form.id : resourceURL) + '?_with=avatar,roles,permissions';
      }
    },
    mounted: function () {
      this.loading = true;
      let loads = [
        axios.get('/user/role'),
        axios.get('/user/permission')
      ];
      if (this.$route.params.id) {
        this.form.id = this.$route.params.id;
        loads.push(axios.get(this.resource));
      }

      Promise.all(loads).then(results => {
        this.roles = results[0].data.data;
        this.permissions = results[1].data.data;
        if (results.length > 2) {
          this.form = results[2].data.data;
          this.form.roles = this.form.roles.map(role => role.id);
          this.form.permissions = this.form.permissions.map(permission => permission.id);
        }
        this.loading = false;
      }).catch(err => {
        this.loading = false;
      });
    },
    methods: {
      onSave: function (event) {
        this._onSave(event).then(result => {
          this.$router.replace('/user/' + result.data.data.id + '/edit');
          this.form = result.data.data;
          this.form.roles = this.form.roles.map(role => role.id);
          this.form.permissions = this.form.permissions.map(permission => permission.id);
        });
      },
      onCancel: function (event) {
        this.$router.back();
      },
    }
  };
</script>

<style lang="scss" scoped>
  .role-selector, .permission-selector {
    width: 100%;
  }
</style>
