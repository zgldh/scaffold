<template>
  <div class="image-uploader">
    <slot><h4>{{$t('components.image_uploader.title')}}</h4></slot>
    <el-upload
      class="image-uploader-core"
      :headers="avatarHeader"
      :action="avatarUploadURL"
      :on-preview="handlePreview"
      :on-remove="handleRemove"
      :on-success="handleSuccess"
      :file-list="fileList"
      list-type="picture">
      <el-button size="small" type="primary">{{$t('components.image_uploader.button_text')}}</el-button>
      <div slot="tip" class="el-upload__tip">{{$t('components.image_uploader.note')}}</div>
    </el-upload>
  </div>
</template>

<script type="javascript">
  import ImageCropper from 'vue-image-crop-upload'
  import { getToken } from '@/utils/auth'

  export default {
    name: 'image-uploader',
    components: { ImageCropper },
    props: {
      value: {
        required: true
      },
      multiple: {
        default: false
      }
    },
    data() {
      return {
        fileList: []
      };
    },
    computed: {
      avatarLangType() {
        if (this.$i18n.locale.indexOf('zh-') === 0) {
          return 'zh';
        }
        return this.$i18n.locale;
      },
      avatarUploadURL() {
        return process.env.BASE_API + '/upload';
      },
      avatarHeader() {
        return {
          Authorization: 'bearer ' + getToken()
        };
      }
    },
    watch: {
      value(newValue) {
        this.moveValueToFileList(newValue)
      }
    },
    mounted() {
      this.moveValueToFileList(this.value)
    },
    methods: {
      moveValueToFileList(modelValue) {
        if (!modelValue) {
          return
        }
        if (modelValue.constructor !== Array) {
          modelValue = [modelValue]
        }
        this.fileList = modelValue.map(item => {
          return {
            name: item.name,
            url: item.url
          }
        })
      },
      handleSuccess(response, file, fileList) {
        console.log(response, file, fileList);
        if (fileList.length > 1 && this.multiple === false) {
          fileList.shift()
        }
        if (this.multiple) {
          this.$emit('input', fileList.map(item => item.response.data))
        } else {
          this.$emit('input', fileList[0].response.data)
        }
      },
      handleRemove(file, fileList) {
        console.log(file, fileList);
      },
      handlePreview(file) {
        var url = file.hasOwnProperty('response') ? file.response.data.url : file.url
        window.open(url, '_blank');
      }
    }
  }


</script>

<style lang="scss" scoped>
  .image-uploader {
    .image-uploader-core {
    }
  }
</style>
