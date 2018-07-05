<template>
  <div class="media-previewer" :class="innerType">
    <img v-if="innerType==TYPE_IMAGE" :src="url" @click="openUrl"
         class="media-type--image">

    <el-button v-else size="small" type="text" @click="openUrl"
               class="other-download-button"><i
            class="el-icon-upload"></i> 点击查看
    </el-button>
  </div>
</template>

<script type="javascript">
  import { isImage, isVideo, isAudio } from '@/utils/filetype'

  export default {
    props: {
      url: String,
      type: {
        type: String,
        default: 'auto'
      }
    },
    data() {
      return {
        TYPE_IMAGE: 'image',
        TYPE_VIDEO: 'video',
        TYPE_AUDIO: 'audio',
        TYPE_OTHER: 'other',
        TYPE_AUTO: 'auto',
      }
    },
    computed: {
      innerType()
      {
        if (this.type !== 'auto') {
          return this.type;
        }
        if (isImage(this.url)) {
          return this.TYPE_IMAGE;
        }
        if (isVideo(this.url)) {
          return this.TYPE_VIDEO;
        }
        if (isAudio(this.url)) {
          return this.TYPE_AUDIO;
        }
        return this.TYPE_OTHER;
      }
    },
    created() {
    },
    mounted(){
    },
    watch: {},
    methods: {
      openUrl(){
        window.open(this.url, '_blank');
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../styles/variables";

  .media-previewer {
    padding: 0.5em;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    border: 1px solid $borderLight;
    display: inline-block;
    text-align: center;
    width: 320px;

    &.image {
      height: 240px;
    }

    .media-type--image {
      max-width: 100%;
      max-height: 100%;
    }
    .other-download-button {
      width: 100%;
    }
  }
</style>
