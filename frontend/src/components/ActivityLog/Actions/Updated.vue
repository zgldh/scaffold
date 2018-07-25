<template>
    <div class="log-action-updated">
        <el-table :data="tableData" stripe border size="mini">
            <el-table-column
                    prop="key"
                    :label="$t('components.activity_log.property.name')"
                    width="180">
            </el-table-column>
            <el-table-column
                    prop="old"
                    :label="$t('components.activity_log.property.old')">
            </el-table-column>
            <el-table-column
                    prop="new"
                    :label="$t('components.activity_log.property.new')">
            </el-table-column>
        </el-table>
    </div>
</template>

<script type="javascript">
  import _ from 'lodash'

  export default {
    name: 'log-action-updated',
    components: {},
    props: {
      log: {
        type: Object,
        required: true
      }
    },
    data() {
      return {}
    },
    computed: {
      tableData() {
        // return _.map(this.log.properties, (value, key) => {
        //   return {
        //     key,
        //     value
        //   }
        // })
      }
    },
    mounted() {
    },
    watch: {},
    methods: {}
  }
</script>
<style rel="stylesheet/scss" lang="scss">
    @import "../../../styles/variables.scss";

    .log-action-updated {
    }
</style>

