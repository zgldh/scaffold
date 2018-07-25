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
            <el-table-column
                    prop="new"
                    :label="$t('components.activity_log.property.diff')">
                <semantic-diff class="new-diff" slot-scope="scope" :left="scope.row.old"
                               :right='scope.row.new'></semantic-diff>
            </el-table-column>
        </el-table>
    </div>
</template>

<script type="javascript">
  import _ from 'lodash'
  import SemanticDiff from 'vue-diff-match-patch/src/components/SemanticDiff'

  export default {
    name: 'log-action-updated',
    components: {
      SemanticDiff
    },
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
        return _.map(this.log.properties.old, (old, key) => {
          var newValue = this.log.properties.attributes[key]
          return {
            key: this.$t(this.modelName + '.fields.' + key),
            old: old,
            new: newValue
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

    .log-action-updated {
        .new-diff {
            del {
                color: $danger;
            }
            ins {
                color: $success;
            }
            span.equal {

            }
        }
    }
</style>

