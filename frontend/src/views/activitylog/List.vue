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
            >
                <el-table-column type="expand">
                    <template slot-scope="props">
                        {{props.row}}
                    </template>
                </el-table-column>
                <el-table-column
                        prop="log_name"
                        :label="$t('activity_log.fields.log_name')"
                        sortable="custom"
                        show-overflow-tooltip
                        width="180">
                </el-table-column>
                <el-table-column
                        prop="description"
                        :label="$t('activity_log.fields.description')"
                        sortable="custom"
                        show-overflow-tooltip>
                </el-table-column>
                <el-table-column
                        prop="causer_id"
                        :label="$t('activity_log.fields.causer_id')"
                        sortable="custom"
                        searchable="false"
                        show-overflow-tooltip>
                    <template slot-scope="scope">
                        {{scope.row.causer?scope.row.causer.name:null}}
                    </template>
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        :label="$t('global.fields.created_at')"
                        sortable="custom"
                        searchable="false"
                        show-overflow-tooltip>
                </el-table-column>
            </zgldh-datatables>
        </el-col>
    </el-row>
</template>

<script type="javascript">
  import { DeleteConfirm } from '@/utils/message'
  import { UserIndex, UserDestroy } from '@/api/user'
  import ListMixin from '@/mixins/List'

  export default {
    components: {},
    mixins: [ListMixin],
    computed: {},
    data() {
      let data = {
        actions: null,
        multipleActions: [],
        advanceFilters: [
          {
            Name: () => this.$i18n.t('activity_log.fields.log_name'),
            Field: 'log_name',
            Type: String
          },
          {
            Name: () => this.$i18n.t('activity_log.fields.description'),
            Field: 'description',
            Type: String
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
      loadData: UserIndex,
      handleYes(item) {
        console.log('yes', item);
      },
      handleSkip(item) {
        console.log('skip', item);
      },
      handleConfirm(items) {
        console.log('confirm', items);
      },
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
    .activitylog-list-page {
        margin: 10px 30px;
        .el-table__body-wrapper {
            height: calc(100vh - 310px) !important;
        }
    }
</style>
