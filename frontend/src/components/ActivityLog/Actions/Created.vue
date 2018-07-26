<template>
    <div class="log-action-created">
        <el-table :data="tableData" stripe border size="mini">
            <el-table-column
                    prop="key"
                    :label="$t('components.activity_log.property.name')"
                    width="180">
            </el-table-column>
            <el-table-column
                    prop="value"
                    :label="$t('components.activity_log.property.value')">
            </el-table-column>
        </el-table>
    </div>
</template>

<script type="javascript">
  import _ from 'lodash'

  export default {
    name: 'log-action-created',
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
      modelName() {
        return _.last(this.log.subject_type.split('\\')).toLowerCase()
      },
      tableData() {
        return _.map(this.log.properties.attributes, (value, key) => {
          return {
            key: this.$t(this.modelName + '.fields.' + key),
            value: this.$t(value)
          }
        })
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

    .log-action-created {
    }
</style>

