<template>
  <h3 :class="className" class="editor-title">
    <span v-if="isCreating">{{$t('components.editor_title.create',{name:name})}}</span>
    <span v-else>{{$t('components.editor_title.edit',{name:name})}}</span>
  </h3>
</template>

<script type="javascript">
  export default {
    name: 'editor-title',
    props: {
      className: {
        type: String
      },
      name: {
        type: String,
        default: ''
      }
    },
    data(){
      return {};
    },
    computed: {
      isCreating() {
        return this.$route.path.indexOf('/create') >= 0
      }
    }
  }
</script>

<style lang="scss" scoped>
  .editor-title {
  }
</style>
