<template>
  <el-upload class="upload-component"
             v-loading="loading"
             with-credentials
             name="file"
             :accept="accept"
             :multiple="multiple"
             :headers="headers"
             :data="data"
             :action="action"
             :show-file-list="showFileList"
             :file-list="fileList"
             :list-type="listType"
             :on-preview="handlePreview"
             :on-success="handleSuccess"
             :on-error="handleError"
             :on-remove="handleRemove"
             :before-upload="beforeUpload">
    <el-button size="small" type="primary" :disabled="disabled"><i class="icon-paper-clip"></i>
      {{$t('views.upload.component.upload_button')}}
    </el-button>
    <span slot="tip" class="el-upload__tip">
      <span v-if="maxSize">
        <template v-if="tipOptions.sizeUnit === 'KB'">
          {{$t('views.upload.component.max_size_kb',{count:maxSize})}}.
        </template>
        <template v-else>
          {{$t('views.upload.component.max_size_mb',{count:maxSize})}}.
        </template>
      </span>
      <span v-if="maxLength && tipOptions.lengthShow !== false">
        {{$t('views.upload.component.max_length',{count:maxLength})}}.
      </span>
      <span v-if="dimensions">
        {{$t('views.upload.component.best_dimensions') + dimensions}}.
      </span>
    </span>
    <span class="upload-component--error" v-if="error">{{error}}</span>
  </el-upload>
</template>

<script type="text/javascript">
  import { getXsrfToken } from 'resources/assets/js/commons/Utils.js';
  import { loadLanguages } from 'resources/assets/js/commons/LanguageHelper';

  export default {
    props: {
      'value': {
        required: true
      },
      'data': {
        type: Object,
        default: function () {
          return {}
        },
        required: false
      },
      'action': {
        type: String,
        default: '/upload',
        required: false
      },
      'listType': {
        type: String,
        default: 'text', //text | picture | picture-card
        required: false
      },
      'accept': {
        type: String,
        default: '*/*',
        required: false
      },
      // MB
      'maxSize': {
        type: Number,
        default: 2,
        required: false
      },
      'maxLength': {
        type: Number,
        default: 0,
        required: false
      },
      'tipOptions':{
        type: Object,
        default: ()=>{
          return {
            sizeUnit: 'MB',
            lengthShow: true
          }
        },
        required: false
      },
      'multiple': {
        type: Boolean,
        default: false,
        required: false
      },
      'showFileList': {
        type: Boolean,
        default: true,
        required: false
      },
      'dimensions': {
        type: String,
        default: '',
        required: false
      },
      'way': {
        type:String,
        default:'',
        required:false
      }
    },
    data() {
      return {
        fileList: [],
        error: '',
        loading: false,
        uploadingCount: 0,
        disabled: false
      };
    },
    computed: {
      headers: function () {
        let headers = {
          'Accept': 'application/json, text/plain, */*',
          'X-XSRF-TOKEN': getXsrfToken()
        };
        return headers;
      }
    },
    watch: {
      value: function (newValue, oldValue) {
        if (newValue) {
          if (this.multiple) {
            if (this.uploadingCount === 0) {
              this.fileList = JSON.parse(JSON.stringify(this.value));
            }
          }
          else {
            this.fileList = [this.value];
          }
        }
        else {
          this.fileList = [];
        }
      },
      fileList: function (newValue) {
        if (newValue.length >= this.maxLength && this.maxLength > 0) {
          this.disabled = true;
        } else {
          this.disabled = false;
        }
      }
    },
    mounted: function () {
      loadLanguages('views.upload.component').then(() => {
        this.$forceUpdate();
      });
      if (this.value) {
        if (this.multiple) {
          this.fileList = JSON.parse(JSON.stringify(this.value));
        }
        else {
          this.fileList = [this.value];
        }
      }

    },
    methods: {
      handlePreview(file) {
        var url = file.url;
        window.open(url, '_blank');
      },
      handleSuccess(res, file, fileList) {
        this.uploadingCount--;
        if (this.multiple) {
          this.fileList = fileList;
          if (this.uploadingCount === 0) {
            this.$emit('input', this.fileList.map(item => item.hasOwnProperty('response') ? item.response.data : item));
          }
        }
        else {
          this.fileList = [res.data];
          this.$emit('input', res.data);
        }
        this.loading = false;
      },
      handleError(res, file, fileList) {
        let json = JSON.parse(res.message.substr(res.message.indexOf('{')));
        this.$message.error(json.message);
        this.loading = false;
      },
      handleRemove(file, fileList) {
        var vm = this;
        return axios.post('/upload/' + file.id, {_method: 'delete', way: vm.way}).then(result => {
          if (result.status == 200) {
            if (vm.multiple) {
              var fileIndex = vm.fileList.findIndex(item => {
                return item.id == result.data.data.id;
              });
              vm.fileList.splice(fileIndex, 1);
              this.$emit('input', vm.fileList);
            }
            else {
              vm.fileList = [];
              this.$emit('input', null);
            }
            return true;
          }
          else {
            throw new Error('delete failed');
          }
        });
      },
      beforeUpload(file) {
        if (this.fileList.length + this.uploadingCount >= this.maxLength && this.maxLength > 0) {
          return false;
        }

        const isLt2M = this.tipOptions.sizeUnit === 'KB' ? file.size / 1024 < this.maxSize : file.size / 1024 / 1024 < this.maxSize;
        if (!isLt2M) {
          let errorMessage =  this.tipOptions.sizeUnit === 'KB' ? this.$i18n.t('views.upload.component.max_size_kb', {count: this.maxSize}) : this.$i18n.t('views.upload.component.max_size_mb', {count: this.maxSize})
          this.$message.error(errorMessage);
        }

        let result = isLt2M;
        if (result) {
          this.loading = true;
          this.uploadingCount++;
        }
        return result;
      }
    }
  };
</script>

<style lang="scss">
  .upload-component {
    img.el-upload-list__item-thumbnail {
      width: auto;
    }
  }
</style>
