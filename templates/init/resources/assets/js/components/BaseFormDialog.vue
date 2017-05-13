<template>
  <div class="modal fade" role="dialog" id="form-dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="关闭">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">{{creating?creatingTitle:editingTitle}}</h4>
        </div>

        <div class="modal-body">
          <form class="form-horizontal" id="editing-form" @submit="onSave">
            <slot name="fields" :item="item" :creating="creating"></slot>
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
  import {mapState} from 'vuex';
  import {alert} from 'resources/assets/js/components/SweetAlertDialogs';

  let formDialog = null;
  let config = {
    props: ['creatingTitle', 'editingTitle'],
    data: function () {
      return {
        saving: false
      };
    },
    mounted: function () {
      let me = this;
      formDialog = $('#form-dialog');
      formDialog.modal({show: false});
      formDialog.on('hidden.bs.modal', function () {
        me.closedDialog();
      });
    },
    computed: {
      item: {
        get () {
          return this.$store.state.editingItem;
        },
        set (value) {
          this.$store.commit('setEditingItem', value);
        }
      },
      editing: {
        get(){
          return this.$store.state.editing;
        }
      },
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
    watch: {
      editing: function (val) {
        if (val) {
          formDialog.modal('show');
          formDialog.find('input[type="file"]').val(null);
        }
        else {
          formDialog.modal('hide');
        }
      }
    },
    methods: {
      closedDialog: function () {
        this.$store.dispatch('endItemEditing', false);
      },
      onSave: function (event) {
        var me = this;
        this.saving = true;
        this.$store.dispatch('endItemEditing', true).then(function (result) {
          me.saving = false;
          formDialog.modal('hide');
        }).catch(function (err) {
          me.saving = false;
          console.log('api call error:', me, err);
          if (err.status !== 422) {
            alert(err.data.messages);
          }
        });
        event.preventDefault();
        event.stopPropagation();
        return false;
      }
    }
  };
  export default config
</script>