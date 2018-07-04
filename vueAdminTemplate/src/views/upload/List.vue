<template>
  <el-row class="upload-list-page">
    <el-col :span="24">
      <list-title :name="$t('upload.title')"></list-title>

      <zgldh-datatables ref="table"
                        :source="loadData"
                        :actions="actions"
                        :multiple-actions="multipleActions"
                        :filters="advanceFilters"
                        :title="$t('upload.title')"
                        :export-columns="exportColumns"
      >
        <el-table-column
                prop="name"
                :label="$t('upload.fields.name')"
                sortable="custom"
                show-overflow-tooltip>
          <template slot-scope="scope">
            <el-button size="small" type="text" @click="onCopy(scope.row.name)">
              {{scope.row.name}}
            </el-button>
            <p class="name-column-description" v-if="scope.row.description">
              {{scope.row.description}}</p>
          </template>
        </el-table-column>
        <el-table-column
                prop="disk"
                :label="$t('upload.fields.disk')"
                sortable="custom"
                show-overflow-tooltip>
        </el-table-column>
        <el-table-column
                prop="path"
                :label="$t('upload.fields.path')"
                sortable="custom"
                show-overflow-tooltip>
          <template slot-scope="scope">
            <el-button size="small" type="text" @click="onCopy(scope.row.path)">
              {{scope.row.path}}
            </el-button>
          </template>
        </el-table-column>
        <el-table-column
                prop="size"
                :label="$t('upload.fields.size')"
                sortable="custom"
                show-overflow-tooltip>
        </el-table-column>
        <el-table-column
                prop="type"
                :label="$t('upload.fields.type')"
                sortable="custom"
                show-overflow-tooltip>
        </el-table-column>
        <el-table-column
                prop="user.name"
                :label="$t('user.fields.name')"
                sortable="custom"
                show-overflow-tooltip>
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
  import { MessageBox } from 'element-ui'
  import {
    DeleteConfirm,
    TextCopyDialog,
    SuccessMessage,
    ErrorMessage
  } from '@/utils/message'
  import { UploadIndex, UploadDestroy, UploadBundle } from '@/api/upload'
  import ListMixin from '@/mixins/List'

  export default {
    components: {},
    mixins: [ListMixin],
    computed: {},
    data (){
      let data = {
        actions: [
          {
            Title: () => this.$i18n.t('global.terms.download'),
            Handle: this.handleDownload
          },
          {
            Title: () => this.$i18n.t('global.terms.edit'),
            Handle: this.handleEdit
          },
          {
            Title: () => this.$i18n.t('global.terms.delete'),
            Handle: this.handleDelete
          },
          180
        ],
        multipleActions: [
          {
            Title: () => this.$i18n.t('global.terms.delete'),
            TargetCare: true,
            Handle: this.handleMultipleDelete
          }
        ],
        advanceFilters: [
          {
            Name: () => this.$i18n.t('upload.fields.name'),
            Field: 'name',
            Type: String,
            ComponentParameters: {
              Like: true
            }
          },
          {
            Name: () => this.$i18n.t('upload.fields.disk'),
            Field: 'disk',
            Type: String,
            ComponentParameters: {
              Like: true
            }
          },
          {
            Name: () => this.$i18n.t('user.fields.name'),
            Field: 'user.name',
            Type: String,
            ComponentParameters: {
              Like: true
            }
          },
          {
            Name: () => this.$i18n.t('upload.fields.size'),
            Field: 'size',
            Type: 'Select',
            ComponentParameters: {
              Multiple: false,
              Items: [
                {
                  Title: '< 100KB',
                  Value: 'interval:[0,102400]'
                },
                {
                  Title: '100KB ~ 1MB',
                  Value: 'interval:(102400,1048576]'
                },
                {
                  Title: '1MB ~ 10MB',
                  Value: 'interval:(1048576,10485760]'
                },
                {
                  Title: '10MB ~ 100MB',
                  Value: 'interval:(10485760,104857600]'
                },
                {
                  Title: '100MB <',
                  Value: 'interval:(104857600,Infinity]'
                }
              ]
            }
          },
          {
            Name: () => this.$i18n.t('global.fields.created_at'),
            Field: 'created_at',
            Type: Date,
            ComponentParameters: {
              Type: 'daterange'
            }
          },
        ],
        exportColumns: {
          "name": this.$t('upload.fields.name'),
          "description": this.$t('upload.fields.description'),
          "disk": this.$t('upload.fields.disk'),
          "path": this.$t('upload.fields.path'),
          "size": this.$t('upload.fields.size'),
          "type": this.$t('upload.fields.type'),
          "user.name": this.$t('user.fields.name'),
          "created_at": this.$t('global.fields.created_at'),
        }
      };
      return data;
    },
    mounted()
    {
    },
    methods: {
      loadData (parameters) {
        parameters += "&_with=user";
        return UploadIndex(parameters)
      },
      handleMultipleDelete(items)
      {
        // TODO multiple works are too complex.
        var ids = items.map(item => item.id);
        var names = items.map(item => item.name).join(', ');
        DeleteConfirm(names, () => UploadBundle('delete', ids), false).then(({ data }) => {
          data.forEach(item => this.$refs.table.removeItem(item.index))
          if (result.message.length) {
            this.$nextTick(() => {
              result.message.forEach(item => this.$refs.table.addRowMessage(item.index, item.message, 'error'));
            })
          } else {
            SuccessMessage(this.$t('messages.delete_confirm.success_text', { name: names }))();
          }
        });
      },
      handleEdit(item)
      {
        this.$router.push({ path: `/upload/${item.id}/edit` })
      },
      handleDownload(item)
      {
        window.open(item.url, '_blank');
      },
      handleDelete(item)
      {
        return DeleteConfirm(item.name, () => UploadDestroy(item.id)).then(() => this.$refs.table.removeItem(item))
      },
      onCopy(message)
      {
        this.$copyText(message).then(SuccessMessage(this.$t('messages.text_copy.complete')), (e) => {
          return TextCopyDialog(message)
        })
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  .upload-list-page {
    margin: 10px 30px;
    .el-table__body-wrapper {
      height: calc(100vh - 310px) !important;
    }
    p.name-column-description {
      margin: 0;
    }
  }
</style>
