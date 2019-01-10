<template>
  <div class="image-uploader">
    <slot><h4>{{$t('components.image_uploader.title')}}</h4></slot>
    <el-upload
      class="image-uploader-core"
      :headers="avatarHeader"
      :action="avatarUploadURL"
      :on-preview="handlePreview"
      :on-progress="handleProgress"
      :on-remove="handleRemove"
      :on-success="handleSuccess"
      :on-exceed="handleExceed"
      :before-upload="beforeUpload"
      :file-list="fileList"
      :multiple="multiple"
      :limit="max"
      accept="image/*"
      list-type="picture">
      <el-button size="small" type="primary">{{$t('components.image_uploader.button_text')}}</el-button>
      <div slot="tip" class="el-upload__tip">{{$t('components.image_uploader.note')}}</div>
    </el-upload>
  </div>
</template>

<script type="javascript">
  import { getToken } from '@/utils/auth'
  import { WarningMessage } from "../../utils/message";

  export default {
    name: 'image-uploader',
    components: {},
    props: {
      value: {
        required: true
      },
      multiple: {
        default: false
      },
      max: {
        type: Number,
        default: null
      },
      debug: {
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
        this.fileList = JSON.parse(JSON.stringify(modelValue))
      },
      handleSuccess(response, file, fileList) {
        this.log(response, file, fileList);
        if (this.multiple && this.max !== null && this.max < fileList.length) {
          fileList.shift()
        } else if (this.multiple === false && 1 < fileList.length) {
          fileList.shift()
        }
        this.emit(fileList);
      },
      handleRemove(file, fileList) {
        this.emit(fileList);
        this.log(file, fileList);
      },
      handlePreview(file) {
        var url = file.hasOwnProperty('response') ? file.response.data.url : file.url
        window.open(url, '_blank');
      },
      handleExceed(file, fileList) {
        WarningMessage(this.$t('components.image_uploader.exceed', { max: this.max }))();
      },
      handleProgress(event, file, fileList) {
        this.log(event, file, fileList);
      },
      beforeUpload(file) {
        this.log(file)
        return true;
      },
      emit(fileList) {
        if (this.multiple) {
          this.$emit('input', fileList.map(item => item.response ? item.response.data : item))
        } else {
          this.$emit('input', fileList[0].hasOwnProperty('response') ? fileList[0].response.data : fileList[0])
        }
      },
      log() {
        if (this.debug) {
          console.log.apply(arguments)
        }
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
