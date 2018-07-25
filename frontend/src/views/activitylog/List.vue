<template>
    <el-row class="activitylog-list-page">
        <el-col :span="24">
            <list-title :name="$t('activity_log.title')"></list-title>

            <zgldh-datatables ref="table"
                              :source="loadData"
                              :enableSelection="false"
                              :actions="actions"
                              :multiple-actions="multipleActions"
                              :filters="advanceFilters"
                              :title="$t('activity_log.title')"
                              :row-class-name="rowClassName"
            >
                <el-table-column type="expand">
                    <log-detail slot-scope="scope" :log="scope.row"/>
                </el-table-column>
                <el-table-column
                        prop="description"
                        :label="$t('activity_log.fields.description')"
                        :sortable="false"
                        show-overflow-tooltip>
                    <log-description slot-scope="scope" :log="scope.row"/>
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        :label="$t('activity_log.fields.created_at')"
                        sortable="custom"
                        searchable="false"
                        show-overflow-tooltip
                        width="180">
                </el-table-column>
            </zgldh-datatables>
        </el-col>
    </el-row>
</template>

<script type="javascript">
  import { DeleteConfirm } from '@/utils/message'
  import { ActivityLogIndex, ActivityLogShow } from '@/api/activityLog'
  import ListMixin from '@/mixins/List'
  import LogDetail from '@/components/ActivityLog/LogDetail'
  import LogDescription from '@/components/ActivityLog/LogDescription'
  import store from '@/store'

  export default {
    components: {
      LogDetail,
      LogDescription
    },
    mixins: [ListMixin],
    computed: {},
    data() {
      let data = {
        actions: null,
        multipleActions: [],
        advanceFilters: [
          {
            Name: () => this.$i18n.t('activity_log.terms.description_search'),
            Field: 'description',
            Type: 'Select',
            ComponentParameters: {
              Multiple: true,
              Items: [
                {
                  Title: () => this.$i18n.t('activity_log.type.login'),
                  Value: 'login'
                },
                {
                  Title: () => this.$i18n.t('activity_log.type.logout'),
                  Value: 'logout'
                },
                {
                  Title: () => this.$i18n.t('activity_log.type.created'),
                  Value: 'created'
                },
                {
                  Title: () => this.$i18n.t('activity_log.type.updated'),
                  Value: 'updated'
                },
                {
                  Title: () => this.$i18n.t('activity_log.type.deleted'),
                  Value: 'deleted'
                },
              ]
            }
          },
          {
            Name: () => this.$i18n.t('global.fields.created_at'),
            Field: 'created_at',
            Type: Date
          },
        ]
      };
      return data;
    },
    mounted() {
    },
    methods: {
      loadData: (parameters) => {
        parameters += "&_with=causer";
        return ActivityLogIndex(parameters)
      },
      handleYes(item) {
        console.log('yes', item);
      },
      handleSkip(item) {
        console.log('skip', item);
      },
      handleConfirm(items) {
        console.log('confirm', items);
      },
      rowClassName({ row, rowIndex }) {
        if (store.state.activityLog.actionsNotExpandable.includes(row.description)) {
          return 'not-expandable'
        }
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
    .activitylog-list-page {
        margin: 10px 30px;
        .zgldh-datatables > .el-table > .el-table__body-wrapper {
            height: calc(100vh - 310px) !important;
        }
        .not-expandable {
            .el-table__expand-icon {
                display: none;
            }
        }
    }
</style>
