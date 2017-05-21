<template>
  <div class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" :style="{ width: dialogWidth}">
      <slot></slot>
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
</template>

<script type="javascript">
  import Promise from 'promise';

  export default{
    props: {
      dialogWidth:String
    },
    data(){
      return {
        promise: null,
        promiseResolve: null,
        promiseReject: null
      };
    },
    mounted: function () {
      let me = this;
      var dialog = $(this.$el);
      dialog.modal({show: false});
      dialog.on('shown.bs.modal', function () {
        me.promiseResolve(me);
      });
      dialog.on('hidden.bs.modal', function () {
        me.closedDialog();
      });
    },
    computed: {},
    methods: {
      open: function () {
        var me = this;
        var dialog = $(this.$el);
        dialog.modal('show');
        this.promise = new Promise(function (resolve, reject) {
          me.promiseResolve = resolve;
          me.promiseReject = reject;
        });
        return this.promise;
      },
      close: function () {
        $(this.$el).modal('hide');
      },
      closedDialog: function () {
        if (this.promiseReject) {
          this.promise = null;
          this.promiseResolve = null;
          this.promiseReject = null;
        }
      }
    }
  };
</script>

<style lang="scss">

</style>