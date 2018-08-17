<template>
    <el-row class="setting-list-page list-page">
        <el-col :span="24">
            <list-title :name="$t('setting.title')"></list-title>

            <zgldh-datatables :source="loadData"
                              :actions="actions"
                              :multiple-actions="multipleActions"
                              :filters="advanceFilters"
                              :title="$t('setting.title')"
            >
                                <el-table-column
                        prop="name"
                        :label="$t('setting.fields.name')"
                        sortable="custom"
                        searchable="true"
                        show-overflow-tooltip>
                                    </el-table-column>

                                <el-table-column
                        prop="value"
                        :label="$t('setting.fields.value')"
                        sortable="custom"
                        searchable="true"
                        show-overflow-tooltip>
                                    </el-table-column>

                                <el-table-column
                        prop="type"
                        :label="$t('setting.fields.type')"
                        sortable="custom"
                        searchable="true"
                        show-overflow-tooltip>
                                    </el-table-column>

                            </zgldh-datatables>
        </el-col>
    </el-row>
</template>

<script type="javascript">
  import { DeleteConfirm } from '@/utils/message'
  import { SettingIndex, SettingDestroy } from '@/api/setting'
  import ListMixin from '@/mixins/List'

  export default {
    components: {},
    mixins:[ListMixin],
    computed: {
            },
    data (){
      let data = {
        actions: [
          {
            Title: () => this.$i18n.t('global.terms.edit'),
            Handle: this.handleEdit
          },
          {
            Title: () => this.$i18n.t('global.terms.delete'),
            Handle: this.handleDelete
          },
          {
            Title: () => this.$i18n.t('global.terms.yes'),
            More: true,
            Handle: this.handleYes
          },
          {
            Title: () => this.$i18n.t('global.terms.skip'),
            More: true,
            Handle: this.handleSkip
          },
        ],
        multipleActions: [
          {
            Title: () => this.$i18n.t('global.terms.create'),
            Handle: this.handleCreate
          },
          {
            Title: () => this.$i18n.t('global.terms.confirm'),
            More: true,
            TargetCare: true,
            Handle: this.handleConfirm
          },
        ],
        advanceFilters: [
          {
            Name: () => this.$i18n.t('setting.fields.name'),
            Field: 'name',
            Type: String
          },
          {
            Name: () => this.$i18n.t('setting.fields.value'),
            Field: 'value',
            Type: String
          },
          {
            Name: () => this.$i18n.t('setting.fields.type'),
            Field: 'type',
            Type: String
          },
        ]
      };
      return data;
    },
    mounted()
    {
    },
    methods: {
      loadData: (parameters) => {
        var _with = '';
        parameters += "&_with=" + _with;
        return SettingIndex(parameters);
      },
      handleCreate(items)
      {
        this.$router.push({ path: `/setting/setting/create` })
      },
      handleEdit(item)
      {
        this.$router.push({ path: `/setting/setting/${item.id}/edit` })
      },
      handleDelete(item)
      {
        DeleteConfirm(item.name, () => SettingDestroy(item.id)).then(() => this.$refs.table.removeItem(item))
      },
      handleYes(item)
      {
        console.log('yes', item);
      },
      handleSkip(item)
      {
        console.log('skip', item);
      },
      handleConfirm(items)
      {
        console.log('confirm', items);
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  .setting-list-page {
    margin: 10px 30px;
    .el-table__body-wrapper {
      height: calc(100vh - 310px) !important;
    }
  }
</style>
