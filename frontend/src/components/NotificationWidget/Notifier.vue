<template>
  <el-tag class="notifier" v-if="notifier">
    <img v-if="notifier.avatar_url" :src="notifier.avatar_url"/>
    {{name}}
  </el-tag>
</template>

<script type="javascript">
  import store from '@/store'

  export default {
    name: 'notifier',
    mixins: [],
    components: {},
    props: {
      notifier: {
        required: true
      }
    },
    data() {
      return {}
    },
    computed: {
      name(){
        return this.notifier ? this.notifier.name : ''
      }
    },
    watch: {},
    methods: {}
  }
</script>
<style rel="stylesheet/scss" lang="scss">
  @import "../../styles/variables";

  .notifier {
  }
</style>
