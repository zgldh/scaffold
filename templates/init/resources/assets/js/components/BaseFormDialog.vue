<template>
  <div class="modal fade form-dialog" role="dialog">
    <div class="modal-dialog" v-bind:class="{'modal-lg':size=='lg','modal-xl':size=='xl'}">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="关闭">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">{{creating?creatingTitle:editingTitle}}</h4>
        </div>

        <div class="modal-body">
          <form class="form-horizontal" @submit="onSave">
            <slot name="fields" v-if="item" :item="item" :creating="creating"></slot>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-flat btn-default pull-left" data-dismiss="modal">关闭</button>
          <button type="submit" form="editing-form" class="btn btn-flat btn-primary" @click="onSave" :disabled="saving">
            {{saving?"保存中...":"保存"}}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script type="javascript">
  import {alert} from 'resources/assets/js/components/SweetAlertDialogs';
  import Promise from 'promise';
  import ErrorsBuilder from 'resources/assets/js/commons/ErrorsBuilder.js';

  let config = {
    props: {
      'creatingTitle': String,
      'editingTitle': String,
      'size': {
        type: String,
        default: 'md',
        validator: function (value) {
          return ['md', 'lg', 'xl'].indexOf(value) >= 0;
        }
      }
    },
    data: function () {
      return {
        item: null,
        saving: false,
        promise: null,
        promiseResolve: null,
        promiseReject: null,
      };
    },
    mounted: function () {
      let me = this;
      var dialog = $(this.$el);
      dialog.modal({show: false});
      dialog.on('hidden.bs.modal', function () {
        me.closedDialog();
      });
    },
    computed: {
      creating: {
        get(){
          if (this.item) {
            return this.item.id == null;
          }
          else {
            return true;
          }
        }
      }
    },
    methods: {
      open: function (item) {
        this.item = $.extend({}, item);
        this.item.$errors = ErrorsBuilder();
        this.saving = false;

        var me = this;
        var dialog = $(this.$el);
        dialog.modal('show');
        dialog.find('input[type="file"]').val(null);
        // this.promise = new Promise(function (resolve, reject) {
        //   me.promiseResolve = resolve;
        //   me.promiseReject = reject;
        // });
        this.promise = {
          mainCallback: null,
          _promise: new Promise(function (resolve, reject) {
            me.promiseResolve = resolve;
            me.promiseReject = reject;
          }),
          then: function (callback) {
            me.promise.mainCallback = callback;
            return me.promise._promise;
          }
        };
        return this.promise;
      },
      close: function () {
        $(this.$el).modal('hide');
      },
      closeResolve: function (data) {
        this.close();
        this.promiseResolve(data);
        this.promise = null;
        this.promiseResolve = null;
        this.promiseReject = null;
      },
      closedDialog: function () {
        if (this.promiseReject) {
          this.promiseReject(this.item);
          this.promise = null;
          this.promiseResolve = null;
          this.promiseReject = null;
        }
      },
      onSave: function (event) {
        this.saving = true;
        this.item.$errors.removeAll();
        this.promise.mainCallback({
          item: this.item,
          closeDialog: this.closeResolve,
          showError: this.showError
        });
        event.preventDefault();
        event.stopPropagation();
        return false;
      },
      showError: function (err) {
        this.saving = false;
        var me = this;
        if (err.status == 422) {
          Object.keys(err.body).map(function (key, index) {
            me.item.$errors.set(key, err.body[key]);
          }, 0);
        }
        else {
          alert(err.data.message);
        }
      }
    }
  };
  export default config
</script>