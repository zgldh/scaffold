<template>
  <el-dialog ref="dialog" class="permission-editor-dialog" :title="title"
             :visible.sync="innerVisible">
    <el-form :model="form" :rules="rules" ref="form" label-width="80px">
      <el-form-item label="Model" prop="model" v-if="creating">
        <el-select v-model="form.model">
          <el-option
                  v-for="(item,$index) in models"
                  :key="$index"
                  :label="item"
                  :value="$index">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Action" prop="action" v-if="creating">
        <el-input v-model="form.action" :autofocus="true" auto-complete="off"
                  ref="actionInput"></el-input>
      </el-form-item>
      <el-form-item v-if="creating===false">
        <p class="text-warning">{{$t('components.permission_editor_dialog.warning')}}</p>
      </el-form-item>
      <el-form-item :label="$t('permission.fields.name')" prop="name">
        <el-input v-model="form.name" auto-complete="off" :disabled="creating"></el-input>
      </el-form-item>
      <el-form-item :label="$t('permission.fields.label')" prop="label">
        <el-input v-model="form.label" auto-complete="off"
                  :disabled="isDefaultAction"></el-input>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click="innerVisible = false">{{$t('global.terms.cancel')}}</el-button>
      <el-button type="primary" @click="isCreating?onCreate():onUpdate()"
                 :disabled="!changed" :loading="loading">
        {{$t('global.terms.save')}}
      </el-button>
    </div>
  </el-dialog>
</template>

<script type="javascript">
  import EditorMixin from '@/mixins/Editor'
  import { SuccessMessage } from '@/utils/message'
  import { PermissionUpdate, PermissionStore } from '@/api/user'
  import { SplitModelAction, IsDefaultAction, PermissionLang } from '@/utils/permission'

  export default {
    name: 'permission-editor-dialog',
    mixins: [EditorMixin],
    props: {
      models: {
        type: Object,
        default: () => {
          return {}
        }
      }
    },
    data() {
      return {
        creating: true, // true: creating; false: editing
        rules: {
          name: {
            required: true,
            type: 'string',
            trigger: ['blur', 'change']
          },
          model: {
            required: true,
            type: 'string',
            trigger: ['blur', 'change']
          },
          action: {
            required: true,
            type: 'string',
            trigger: ['blur', 'change']
          },
        },
        form: {
          id: 0,
          name: '',
          label: '',
          model: '',
          action: '',
        },
        oldForm: {},
        isDefaultAction: false,
        innerVisible: false,
        resolve: null,
        reject: null
      }
    },
    computed: {
      isCreating() {
        return this.creating
      },
      changed(){
        return _.isEqual(this.form, this.oldForm) === false
      },
      title(){
        if (this.creating) {
          return this.$t('global.terms.create') + ' ' + this.$t('permission.title')
        }
        else {
          return this.$t('global.terms.edit') + ' ' + this.$t('permission.title')
        }
      }
    },
    watch: {
      'form.model'(newValue){
        this.form.name = this.form.model + '@' + this.form.action;
      },
      'form.action'(newValue){
        this.form.name = this.form.model + '@' + this.form.action;
      },
    },
    methods: {
      create(){
        this.form.id = 0
        this.form.label = ''
        this.form.model = _.keys(this.models)[0]
        this.form.action = 'newAction'
        this.form.name = this.form.model + '@' + this.form.action;
        var result = this.show(this.form);
        this.creating = true
        this.$nextTick(() => {
          this.$refs.actionInput.focus();
        })
        return result
      },
      show(data){
        if (!data.hasOwnProperty('model')) {
          data.model = ''
        }
        if (!data.hasOwnProperty('action')) {
          data.action = ''
        }
        this.form = data;
        this.innerVisible = true;

        [this.form.model, this.form.action] = SplitModelAction(this.form.name)
        this.isDefaultAction = IsDefaultAction(this.form.action)
        this.oldForm = { ...this.form }
        this.loading = false
        this.creating = false

        return new Promise((resolve, reject) => {
          this.resolve = resolve
          this.reject = reject
        })
      },
      onCreate(){
        var responseData;
        this.$refs.form.validate().then(valid => {
          this.loading = true;
          return PermissionStore(this.form);
        })
                .then(res => {
                  responseData = res.data;
                  this.form.id = res.data.id;
                  this.form.name = res.data.name;
                  this.form.label = res.data.label;
                })
                .then(SuccessMessage(this.$t('global.terms.save_completed')))
                .then(res => this.loading = false)
                .then(res => this.innerVisible = false)
                .then(res => this.resolve(responseData))
                .catch(this.errorHandler);
      },
      onUpdate(){
        var responseData;
        this.$refs.form.validate().then(valid => {
          this.loading = true;
          return PermissionUpdate(this.form.id, {
            name: this.form.name,
            label: this.form.label
          });
        })
                .then(res => {
                  responseData = res.data;
                  this.form.id = res.data.id;
                  this.form.name = res.data.name;
                  this.form.label = res.data.label;
                })
                .then(SuccessMessage(this.$t('global.terms.save_completed')))
                .then(res => this.loading = false)
                .then(res => this.innerVisible = false)
                .then(res => this.resolve(responseData))
                .catch(this.errorHandler);
      }
    }
  }
</script>
<style rel="stylesheet/scss" lang="scss">
  @import "../../styles/variables.scss";

  .permission-editor-dialog {
    .text-warning {
      color: $danger;
    }
  }
</style>

