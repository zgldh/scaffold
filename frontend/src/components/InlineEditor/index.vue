<template>
  <span class="inline-editor">
    <el-input :type="type" ref="input" v-if="isModeEditor" v-model="innerValue"
              size="medium">
      <el-button slot="append" icon="el-icon-check" @click="commitEdit"></el-button>
      <el-button slot="append" icon="el-icon-close" @click="cancelEdit"></el-button>
    </el-input>
    <span v-else>{{innerValue}}
      <el-button icon="el-icon-edit" @click="startEdit" size="medium"></el-button>
    </span>
  </span>
</template>

<script type="javascript">
  const MODE_VIEW = 'view';
  const MODE_EDITOR = 'editor';
  export default {
    name: 'inline-editor',
    props: {
      value: {
        required: true
      },
      type: {
        default: 'text'
      },
    },
    data(){
      return {
        mode: MODE_VIEW,
        innerValue: null
      }
    },
    computed: {
      isModeView(){
        return this.mode === MODE_VIEW;
      },
      isModeEditor(){
        return this.mode === MODE_EDITOR;
      }
    },
    mounted(){
    },
    watch: {
      value(newValue){
        this.innerValue = newValue;
      }
    },
    methods: {
      startEdit(){
        this.mode = MODE_EDITOR;
        this.$nextTick(() => {
          var inputElement = this.$refs.input.$el.querySelector('input');
          inputElement.select();
          inputElement.onkeydown = (e) => {
            if (e.keyCode === 13) {
              return false;
            }
          };
          inputElement.onkeyup = (e) => {
            if (e.keyCode === 13) {
              this.commitEdit();
              return false;
            }
            else if (e.keyCode === 27) {
              return this.cancelEdit();
            }
          }
        });
      },
      commitEdit(){
        this.mode = MODE_VIEW;
        if (this.innerValue != this.value) {
          this.$emit('input', this.innerValue);
          this.$emit('change', this.innerValue);
        }
      },
      cancelEdit (){
        this.mode = MODE_VIEW;
        this.innerValue = this.value;
      },
    }
  }
</script>

<style lang="scss" scoped>
  .inline-editor {
  }
</style>
