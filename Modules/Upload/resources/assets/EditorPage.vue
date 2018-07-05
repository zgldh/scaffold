<template>
  <div class="admin-editor-page">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{$t('upload.title')}}
        <small v-if="form.id">{{$t('global.terms.edit')}}</small>
        <small v-else>{{$t('global.terms.create')}}</small>
      </h1>
      <ol class="breadcrumb">
        <li>
          <router-link to="/"><i class="fa fa-dashboard"></i> {{$t('module_dashboard.title')}}</router-link>
        </li>
        <li>
          <router-link to="/upload/list">{{$t('upload.title')}}</router-link>
        </li>
        <li class="active" v-if="form.id">{{$t('global.terms.edit')}}</li>
        <li class="active" v-else>{{$t('global.terms.create')}}</li>
      </ol>
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
          <el-form ref="form" :model="form" label-width="200px" v-loading="loading">
            <el-form-item :label="$t('global.fields.id')" v-if="form.id">
              <el-input v-model="form.id" disabled></el-input>
            </el-form-item>
            <el-form-item :label="$t('upload.fields.name')" prop="name" :error="errors.name">
              <el-input v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item :label="$t('upload.fields.description')" prop="description"
                          :error="errors.description">
              <el-input v-model="form.description"></el-input>
            </el-form-item>
            <el-form-item :label="$t('upload.fields.type')" prop="type" :error="errors.type">
              <el-input v-model="form.type"></el-input>
            </el-form-item>
            <el-form-item :label="$t('upload.fields.disk')" v-if="form.id">
              <el-input v-model="form.disk" disabled></el-input>
            </el-form-item>
            <el-form-item :label="$t('upload.fields.path')" v-if="form.id">
              <el-input v-model="form.path" disabled></el-input>
            </el-form-item>
            <el-form-item :label="$t('upload.fields.size')" v-if="form.id">
              <el-input v-model="form.size" disabled></el-input>
            </el-form-item>

            <el-form-item :label="$t('upload.fields.file')" prop="file" :error="errors.file">
              <input type="file" class="form-control" id="field-file" v-on:change="onFileChange" name="file">
              <p class="assist-tip" v-if="form.url">
                <a :href="form.url" target="_blank"><i class="el-icon-view"></i> {{form.url}}</a>
              </p>
            </el-form-item>

            <el-form-item :label="$t('upload.fields.user_id')" v-if="form.id">
              <el-input v-model="form.user_id" disabled></el-input>
            </el-form-item>
            <el-form-item :label="$t('upload.fields.uploadable_id')" v-if="form.id">
              <el-input v-model="form.uploadable_id" disabled></el-input>
            </el-form-item>
            <el-form-item :label="$t('upload.fields.uploadable_type')" v-if="form.id">
              <el-input v-model="form.uploadable_type" disabled></el-input>
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
    <!-- /.content -->

  </div>
</template>

<script type="text/javascript">
  import { mixin } from "resources/assets/js/commons/EditorHelper.js";
  import { loadModuleLanguage } from 'resources/assets/js/commons/LanguageHelper';

  export default  {
    mixins: [
      mixin,
      loadModuleLanguage('module_upload')
    ],
    data: function () {
      return {
        form: {
          id: null,
          name: '',
          description: '',
          type: '',
          file: null
        }
      };
    },
    components: {},
    computed: {
      resource: function () {
        var resourceURL = '/upload';
        return (this.form.id ? resourceURL + '/' + this.form.id : resourceURL);// + '?_with=roles,permissions';
      }
    },
    mounted: function () {
      this.loading = true;
      let loads = [];
      if (this.$route.params.id) {
        this.form.id = this.$route.params.id;
        loads.push(axios.get(this.resource));
      }

      Promise.all(loads).then(results => {
        this.form = results[0].data.data;
        this.loading = false;
      }).catch(() => {
        this.loading = false;
      });
    },
    methods: {
      onSave: function (event) {
        this._onSave(event).then(result => {
          this.$router.replace('/upload/' + result.data.data.id + '/edit');
          this.form = result.data.data;
          this.form.permissions = this.form.permissions.map(permission => permission.id);
        });
      },
      onCancel: function () {
        this.$router.back();
      },
      onFileChange: function (event) {
        this.form.file = event.target.files[0];
      }
    }
  };
</script>

<style lang="scss" scoped>

</style>
