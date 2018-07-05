<template>
  <el-row class="upload-editor-page" v-loading="loading">
    <el-col :span="11">
      <editor-title :name="$t('upload.title')"></editor-title>

      <el-form label-position="right" label-width="80px" :rules="rules" :model="form"
               ref="form">
        <form-item prop="name" :label="$t('upload.fields.name')">
          <el-input v-model="form.name"></el-input>
        </form-item>
        <form-item prop="description" :label="$t('upload.fields.description')">
          <el-input type="textarea" v-model="form.description"></el-input>
        </form-item>
        <form-item prop="disk" :label="$t('upload.fields.disk')">
          <el-input v-model="form.disk" disabled></el-input>
        </form-item>
        <form-item prop="path" :label="$t('upload.fields.path')">
          <el-input v-model="form.path" disabled></el-input>
        </form-item>
        <form-item :label="$t('global.fields.created_at')">
          <el-input v-model="form.created_at" disabled></el-input>
        </form-item>
        <form-item :label="$t('user.fields.name')">
          <el-input v-model="form.user.name" disabled></el-input>
        </form-item>


        <form-item>
          <el-button type="primary" @click="onUpdate()">
            {{$t('global.terms.submit')}}
          </el-button>
          <el-button @click="$router.go(-1)">{{$t('global.terms.back')}}</el-button>
        </form-item>
      </el-form>
    </el-col>
    <el-col :span="11" :offset="1">
      <h3>当前文件</h3>
      <media-previewer :url="form.url"></media-previewer>
      <h3>替换文件</h3>
      <el-upload class="upload-replacement"
                 v-loading="uploading"
                 :headers="uploadReplacementHeaders"
                 :data="uploadReplacementData"
                 :action="uploadReplacementAction"
                 :show-file-list="false"
                 :on-success="onUploadSuccess"
                 :on-error="onUploadError"
                 :before-upload="beforeUpload">
        <el-button size="small" type="primary"><i class="el-icon-upload"></i> 点击上传
        </el-button>
        <span class="el-upload__tip" slot="tip">文件不超过4MB</span>
      </el-upload>
    </el-col>
  </el-row>
</template>

<script type="javascript">
  import { mapState } from 'vuex'
  import { SuccessMessage } from '@/utils/message'
  import { getToken } from '@/utils/auth'
  import { UploadStore, UploadUpdate, UploadShow } from '@/api/upload'
  import store  from '@/store'
  import EditorMixin from '@/mixins/Editor'
  import MediaPreviewer from '@/components/MediaPreviewer'

  export default {
    components: {
      MediaPreviewer
    },
    mixins: [EditorMixin],
    data () {
      return {
        uploading: false,
        rules: {},
        form: {
          id: 0,
          name: '',
          description: '',
          disk: '',
          path: '',
          size: '',
          type: '',
          url: '',
          user: {
            name: ''
          }
        }
      };
    },
    computed: {
      uploadReplacementAction(){
        return process.env.BASE_API + '/upload/' + this.form.id;
      },
      uploadReplacementData(){
        var data = {
          _method: 'put'
        };
        return data;
      },
      uploadReplacementHeaders(){
        return {
          Authorization: 'bearer ' + getToken()
        };
      }
    },
    beforeRouteEnter(to, from, next){
      next();
    },
    mounted () {
      this.fetchData();
    },
    watch: {
      $route: 'fetchData',
    },
    methods: {
      fetchData() {
        if (this.$route.params.id) {
          this.loading = true;
          UploadShow(this.$route.params.id, '_with=user')
                  .then(res => this.setFormData(res.data))
                  .then(res => this.loading = false)
        }
      },
      setFormData(rawFormData){
        this.form = rawFormData
      },
      onUpdate () {
        this.$refs.form.validate().then(valid => {
          this.loading = true;
          return UploadUpdate(this.form.id, '_with=user', this.form)
        })
                .then(res => this.setFormData(res.data))
                .then(SuccessMessage(this.$t('global.terms.save_completed')))
                .then(res => this.loading = false)
                .catch(this.errorHandler);
      },
      onUploadSuccess(result){
        this.uploading = false;
        this.fetchData();
      },
      onUploadError(result){
        console.log('onUploadError', result);
        this.uploading = false;
      },
      beforeUpload(result){
        this.uploading = true;
      },
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  .upload-editor-page {
    margin: 10px 30px;
    .el-upload__tip {
      margin-left: 0.5em;
    }
  }
</style>
