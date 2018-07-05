<template>
    <div class="change-log-widget datatable datatable-loading-section">
        <el-table
                :data="tableData"
                style="width: 100%"
                border
                :default-sort="defaultSort"
                @selection-change="onSelectionChange"
                ref="table"
        >
            <el-table-column
                    :label="$t('views.strategy.development.publish.change_log')"
                    searchable="true"
                    width="120"
                    show-overflow-tooltip>
                <template slot-scope="scope">
                    <span>{{scope.row.causer.firstname}} {{scope.row.causer.lastname}}</span>
                </template>
            </el-table-column>
            <el-table-column
                    searchable="true"
                    width="100"
                    show-overflow-tooltip>
                <template slot-scope="scope">
                    <el-tag type="success" v-if="scope.row.description == 'login'">Login</el-tag>
                    <el-tag type="success" v-if="scope.row.description == 'created'">Added</el-tag>
                    <el-tag type="warning" v-if="scope.row.description == 'updated'">Updated</el-tag>
                    <el-tag type="danger" v-if="scope.row.description == 'deleted'">Deleted</el-tag>
                </template>
            </el-table-column>
            <el-table-column
                    searchable="true">
                <template slot-scope="scope">
                    <span>{{getDescription(scope.row)}}</span>
                </template>
            </el-table-column>
            <el-table-column
                    prop="created_at"
                    sortable="custom"
                    searchable="false"
                    show-overflow-tooltip
                    width="190">
            </el-table-column>
        </el-table>
        <el-row class="tools">
            <el-col :span="6">
        <span class="page-size">
            {{$t('global.terms.page_size_show')}}
            <el-select v-model="pagination.pageSize" style="width: 80px;"
                       @change="onPageSizeChange">
              <el-option
                      v-for="item in pagination.pageSizeList"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value">
              </el-option>
            </el-select>
            {{$t('global.terms.page_size_items')}}
        </span>
            </el-col>
            <el-col :span="6" class="pull-right">
                <el-pagination
                        class="pull-right"
                        @current-change="onPageChange"
                        :current-page="pagination.currentPage"
                        :page-size="pagination.pageSize==-1?1:pagination.pageSize"
                        :layout="pagination.pageSize==-1?'total':'total, prev, pager, next, jumper'"
                        :total="pagination.totalCount">
                </el-pagination>
            </el-col>
        </el-row>
    </div>
</template>

<script type="text/javascript">
    import {mixin} from "resources/assets/js/commons/ListHelpers";
    import {loadLanguages} from 'resources/assets/js/commons/LanguageHelper';

    export default {
        mixins : [
            mixin
        ],
        props  : {
            isAutoload   : {
                default: false
            },
            causerType   : null,
            causerId     : null,
            subjectType  : null,
            subjectId    : null,
            collectorType: null,
            collectorId  : null,
        },
        data   : function () {
            let data = {
                autoload            : this.isAutoload,
                resource            : '/activity_log?_with=causer',
                datatablesParameters: {
                    order  : [{column: 'created_at', dir: 'desc'}],
                    columns: [
                        {
                            name      : 'causer_id',
                            data      : 'causer_id',
                            searchable: true,
                        },
                        {
                            name      : 'causer_type',
                            data      : 'causer_type',
                            searchable: true,
                        },
                        {
                            name      : 'subject_id',
                            data      : 'subject_id',
                            searchable: true,
                        },
                        {
                            name      : 'subject_type',
                            data      : 'subject_type',
                            searchable: true,
                        },
                        {
                            name      : 'collector_id',
                            data      : 'collector_id',
                            searchable: true,
                        },
                        {
                            name      : 'collector_type',
                            data      : 'collector_type',
                            searchable: true,
                        },
                    ]
                },
                searchForm          : {
                    subject    : null,
                    description: null,
                    created_at : null
                }
            };
            return data;
        },
        mounted: function () {
            loadLanguages('views.strategy.development.publish').then(() => {
                this.$forceUpdate();
            });
        },
        beforeMount () {
            if (this.causerId) {
                this.applyAdvanceSearchToColumn('causer_id', '=', this.causerId);
            }
            if (this.causerType) {
                this.applyAdvanceSearchToColumn('causer_type', '=', this.causerType);
            }
            if (this.subjectId) {
                this.applyAdvanceSearchToColumn('subject_id', '=', this.subjectId);
            }
            if (this.subjectType) {
                this.applyAdvanceSearchToColumn('subject_type', '=', this.subjectType);
            }
            if (this.collectorId) {
                this.applyAdvanceSearchToColumn('collector_id', '=', this.collectorId);
            }
            if (this.collectorType) {
                this.applyAdvanceSearchToColumn('collector_type', '=', this.collectorType);
            }
        },
        watch  : {
            causerType   : function (val, old) {
                if (val) {
                    this.applyAdvanceSearchToColumn('causer_type', '=', val);
                } else {
                    this.clearAdvanceSearchToColumn('causer_type', '=');
                }
                this.queryTableData()
            },
            causerId     : function (val, old) {
                if (val) {
                    this.applyAdvanceSearchToColumn('causer_id', '=', val);
                } else {
                    this.clearAdvanceSearchToColumn('causer_id', '=');
                }
                this.queryTableData()
            },
            subjectType  : function (val, old) {
                if (val) {
                    this.applyAdvanceSearchToColumn('subject_type', '=', val);
                } else {
                    this.clearAdvanceSearchToColumn('subject_type', '=');
                }
                this.queryTableData()
            },
            subjectId    : function (val, old) {
                if (val) {
                    this.applyAdvanceSearchToColumn('subject_id', '=', val);
                } else {
                    this.clearAdvanceSearchToColumn('subject_id', '=');
                }
                this.queryTableData()
            },
            collectorType: function (val, old) {
                if (val) {
                    this.applyAdvanceSearchToColumn('collector_type', '=', val);
                } else {
                    this.clearAdvanceSearchToColumn('collector_type', '=');
                }
                this.queryTableData()
            },
            collectorId  : function (val, old) {
                if (val) {
                    this.applyAdvanceSearchToColumn('collector_id', '=', val);
                } else {
                    this.clearAdvanceSearchToColumn('collector_id', '=');
                }
                this.queryTableData()
            },
        },
        methods: {
            load: function () {
                this.queryTableData();
            },
            getDescription(row){
                return row.description + ' the ' + row.subject_type.split('\\').reverse()[0];
            },
        }
    };

</script>

<style lang="scss">
    .change-log-widget {
    }
</style>
