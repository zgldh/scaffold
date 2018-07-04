<template>
  <div class="avatar-editor">
    <slot><h4>{{$t('components.avatar_editor.title')}}</h4></slot>
    <img v-if="avatar" class="avatar" :src="avatar">
    <el-button @click="imageCropperShow=true">
      {{$t('components.avatar_editor.button_text')}}
    </el-button>
    <image-cropper :width="300" :height="300" field="file"
                   :url="avatarUploadURL"
                   :params="params"
                   @crop-success="cropAvatarSuccess"
                   @crop-upload-success="cropUploadSuccess"
                   @crop-upload-fail="cropUploadFail"
                   v-model="imageCropperShow"
                   :headers="avatarHeader"
                   :lang-type="avatarLangType"></image-cropper>
  </div>
</template>

<script type="javascript">
  import ImageCropper from 'vue-image-crop-upload'
  import { getToken } from '@/utils/auth'

  export default {
    name: 'avatar-editor',
    components: { ImageCropper },
    props: {
      avatar: {
        required: true
      },
      userId: {
        type: Number,
        required: false
      }
    },
    data(){
      return {
        imageCropperShow: false,
      };
    },
    computed: {
      avatarLangType(){
        if (this.$i18n.locale.indexOf('zh-') === 0) {
          return 'zh';
        }
        return this.$i18n.locale;
      },
      avatarUploadURL(){
        return process.env.BASE_API + '/user/avatar';
      },
      avatarHeader(){
        return {
          Authorization: 'bearer ' + getToken()
        };
      },
      params(){
        var params = {};
        if (this.userId) {
          params.user_id = this.userId;
        }
        return params;
      },
    },
    watch: {},
    mounted(){
    },
    methods: {
      cropAvatarSuccess(imageDataUrl, field) {
      },
      cropUploadSuccess(jsonData, field) {
        this.$emit('crop-upload-success', { jsonData, field });
      },
      cropUploadFail(status, field) {
        console.log('-------- upload fail --------');
        console.log(status);
        console.log('field: ' + field);
      },
    }
  }


</script>

<style lang="scss" scoped>
  .avatar-editor {
    .avatar {
      width: 300px;
      height: 300px;
      display: block;
      margin-bottom: 1em;
    }
  }
</style>
