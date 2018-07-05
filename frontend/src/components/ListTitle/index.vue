<template>
  <h3 :class="className" class="list-title">
    <span>{{$t('components.list_title',{name:name})}}</span>
  </h3>
</template>

<script type="javascript">
  export default {
    name: 'list-title',
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
    computed: {}
  }
</script>

<style lang="scss" scoped>
  .list-title {
  }
</style>
