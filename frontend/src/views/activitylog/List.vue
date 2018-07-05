<template>
  <el-row class="activitylog-list-page">
    <el-col :span="11">
      <h3>Activity Log List</h3>
      <p>TODO frontend\src\views\activitylog\List.vue</p>
    </el-col>
  </el-row>
</template>

<script type="javascript">
  import { SuccessMessage } from '@/utils/message'

  export default {
    components: {},
    computed: {},
    data (){
      return {
        items: []
      };
    },
    mounted(){
    },
    methods: {}
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  .activitylog-list-page {
    margin: 10px 30px;
  }
</style>
