<template>
  <div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{$t('role.title')}}
        <small v-if="form.id">{{$t('global.terms.edit')}}</small>
        <small v-else>{{$t('global.terms.create')}}</small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="box box-default">

        <div class="box-header with-border">
          <el-button type="default" @click="onCancel" icon="close">{{$t('global.terms.back')}}</el-button>
          <el-button type="primary" @click="onSave" icon="check" :loading="saving||loading">
            {{$t('global.terms.save')}}
          </el-button>
        </div>
        <!-- /.box-header -->

        <!-- form start -->
        <div class="box-body">
          <el-alert class="missing-errors" v-if="missingErrors.length" v-for="errorMessage in missingErrors"
                    :key="errorMessage"
                    :title="errorMessage" type="error" show-icon></el-alert>

          <!-- form start -->
          <el-form ref="form" :model="form" label-width="100px" v-loading="loading">
            <el-form-item :label="$t('global.fields.id')" v-if="form.id">
              <el-input v-model="form.id" disabled></el-input>
            </el-form-item>
            <el-form-item :label="$t('role.fields.name')" prop="name" :error="errors.name">
              <el-input v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item :label="$t('role.fields.label')" prop="label" :error="errors.label">
              <el-input v-model="form.label"></el-input>
            </el-form-item>
            <el-form-item :label="$t('role.fields.permissions')" prop="permissions"
                          :error="errors.permissions">
              <el-select class="permission-selector" v-model="form.permissions" filterable multiple>
                <el-option
                        v-for="permission in permissions"
                        :key="permission.id"
                        :label="permission.label"
                        :value="permission.id">
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item :label="$t('global.fields.created_at')" v-if="form.id">
              <el-input v-model="form.created_at" disabled></el-input>
            </el-form-item>
          </el-form>
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
          <el-button type="default" @click="onCancel" icon="close">{{$t('global.terms.back')}}</el-button>
          <el-button type="primary" @click="onSave" icon="check" :loading="saving||loading">
            {{$t('global.terms.save')}}
          </el-button>
        </div>
      </div>
    </section>
  </div>
</template>

<script type="text/javascript">
  import { mixin } from "resources/assets/js/commons/EditorHelper.js";
  import { loadModuleLanguage } from 'resources/assets/js/commons/LanguageHelper';

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
          label: '',
          permissions: []
        },
        permissions: []
      };
    },
    components: {},
    computed: {
      resource: function () {
        var resourceURL = '/user/role';
        return (this.form.id ? resourceURL + '/' + this.form.id : resourceURL) + '?_with=permissions';
      }
    },
    mounted: function () {
      this.loading = true;
      let loads = [axios.get('/user/permission')];
      if (this.$route.params.id) {
        this.form.id = this.$route.params.id;
        loads.push(axios.get(this.resource));
      }

      Promise.all(loads).then(results => {
        this.permissions = results[0].data.data;
        if (results.length > 1) {
          this.form = results[1].data.data;
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
          this.$router.replace('/user/role/' + result.data.data.id + '/edit');
          this.form = result.data.data;
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
  .permission-selector {
    width: 100%;
  }
</style>
