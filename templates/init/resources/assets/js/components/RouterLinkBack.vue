<template>
  <button @click="onClick" class="btn btn-lg btn-flat btn-default pull-left">
    <slot></slot>
  </button>
</template>

<script type="javascript">

  export default  {
    props: {},
    data: function () {
      return {};
    },
    computed: {},
    mounted: function () {
    },
    destroyed: function () {
    },
    methods: {
      onClick: function () {
        this.$router.back();
      }
    }
  };
</script>

<style lang="scss">
</style>

