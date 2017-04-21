<template>
  <div class="upload-file">
    <div class="btn-group">
      <a class="btn btn-flat btn-sm btn-default" title="查看当前文件" target="_blank"
         v-if="uploadedFile" :href="uploadedFile.url">
        <i class="fa fa-download" aria-hidden="true"></i> <span v-if="!iconOnly">查看当前文件</span>
      </a>
      <button type="button" class="btn btn-flat btn-sm btn-default"
              @click="selectFile()" v-if="isStateIdle()" :title="value?'更换文件':'选择文件'">
        <i class="fa fa-upload" aria-hidden="true"></i> <span v-if="!iconOnly">{{value?'更换文件':'选择文件'}}</span>
      </button>
      <button type="button" class="btn btn-flat btn-sm btn-default" title="上传中，点击取消"
              @click="cancelUpload()" v-if="isUploading()">
        {{ uploadingFile.name }} <span v-if="!iconOnly">上传中，点击取消 </span><i class="fa fa-times" aria-hidden="true"></i>
        <div class="progress progress-xxs active">
          <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"
               :aria-valuenow="uploadingProgress"
               aria-valuemin="0" aria-valuemax="100" :style="'width: '+uploadingProgress+'%'">
            <span class="sr-only" v-if="!iconOnly">{{uploadingProgress}}% Complete (warning)</span>
          </div>
        </div>
      </button>
    </div>
    <input type="file" value="">
  </div>
</template>

<script type="javascript">

  const STATE_IDLE = 'idle';
  const STATE_UPLOADING = 'uploading';

  export default  {
    props: {
      'value': '',
      'action': {
        type: String,
        default: '/upload',
        required: false
      },
      'iconOnly': {
        type: Boolean,
        default: false,
        required: false
      },
      'onUploaded': {
        type: Function,
        default: null,
        required: false
      },
    },
    data: function () {
      return {
        uploadedFile: null,
        fileElement: null,  // 控件
        state: STATE_IDLE,
        uploadingFile: null,
        uploadingProgress: 0,
      };
    },
    mounted: function () {
      let vm = this;
      let el = $(this.$el);
      this.fileElement = el.find('input[type="file"]');
      this.fileElement.on('change', function (e) {
        if (this.files.length > 0) {
          vm.startUpload(this.files[0]);
        }
      });
      this.uploadedFile = this.value;
    },
    watch: {
      value: function (value) {
        // update value
        this.uploadedFile = this.value;
      }
    },
    destroyed: function () {
      $(this.$el).off();
    },
    methods: {
      selectFile: function () {
        this.fileElement.click();
      },
      cancelUpload: function () {
        // TODO cancel AJAX call;
        this.state = STATE_IDLE;
        this.uploadingFile = null;
        this.fileElement.val(null);
      },
      isStateIdle: function () {
        return this.state == STATE_IDLE;
      },
      isUploading: function () {
        return this.state == STATE_UPLOADING;
      },
      startUpload: function (file) {
        let vm = this;
        this.state = STATE_UPLOADING;
        this.uploadingFile = file;
        this.uploadingProgress = 0;
        this.fileElement.val(null);
        // Send AJAX call
        console.log('start upload', file);

        this.$http.post(this.action,
                {file: file},
                {
                  progress: function (event) {
                    vm.uploadingProgress = Math.round((event.loaded / event.total) * 100);
                  }
                })
                .then((response) => {
                  // success callback
                  console.log('upload success', response.body.data);
                  vm.$emit('input', response.body.data);
                  vm.uploadedFile = response.body.data;
                  vm.cancelUpload();
                  if (vm.onUploaded) {
                    vm.onUploaded(response.body.data);
                  }
                }, (response) => {
                  // error callback
                  console.log('upload error', response);
                  vm.cancelUpload();
                });
      }
    }
  };
</script>

<style lang="scss">
  .upload-file {
    border: none;
    input[type="file"] {
      display: none;
    }

    .progress {
      margin: 0;
    }
  }
</style>
