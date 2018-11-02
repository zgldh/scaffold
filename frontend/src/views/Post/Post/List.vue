<template>
    <el-row class="post-list-page list-page">
        <el-col :span="24">
            <list-title :name="$t('post.title')"></list-title>

            <zgldh-datatables ref="table"
                              :source="loadData"
                              :actions="actions"
                              :multiple-actions="multipleActions"
                              :filters="advanceFilters"
                              :title="$t('post.title')"
            >
                <el-table-column
                        prop="content"
                        :label="$t('post.fields.content')"
                        sortable="custom"
                        searchable="true"
                        show-overflow-tooltip>
                </el-table-column>

                <el-table-column
                        prop="category"
                        :label="$t('post.fields.category')"
                        sortable="custom"
                        searchable="true"
                        show-overflow-tooltip>
                </el-table-column>

                <el-table-column
                        prop="status"
                        :label="$t('post.fields.status')"
                        sortable="custom"
                        searchable="true"
                        show-overflow-tooltip>
                </el-table-column>

                <el-table-column
                        prop="created_by"
                        :label="$t('post.fields.user')"
                        sortable="custom"
                        searchable="true"
                        show-overflow-tooltip>
                    <template slot-scope="scope">
                        <span>{{scope.row.user?scope.row.user.name:''}}</span>

                    </template>
                </el-table-column>

            </zgldh-datatables>
        </el-col>
    </el-row>
</template>

<script type="javascript">
  import { DeleteConfirm } from '@/utils/message'
  import { PostIndex, PostDestroy } from '@/api/post'
  import ListMixin from '@/mixins/List'
  import { updateTitle } from '@/utils/browser'

  export default {
    components: {},
    mixins: [ListMixin],
    computed: {
      //      _createdByList() {
//        return this.$store.state.post._createdByList;
//      },
//      _commentsList() {
//        return this.$store.state.post._commentsList;
//      },
//      _coverList() {
//        return this.$store.state.post._coverList;
//      },
    },
    data() {
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
            Name: () => this.$i18n.t('post.fields.content'),
            Field: 'content',
            Type: String
          },
          {
            Name: () => this.$i18n.t('post.fields.category'),
            Field: 'category',
            Type: String
          },
          {
            Name: () => this.$i18n.t('post.fields.status'),
            Field: 'status',
            Type: String
          },
          {
            Name: () => this.$i18n.t('post.fields.created_by'),
            Field: 'created_by',
            Type: String
          },
        ]
      };
      return data;
    },
    mounted() {
      updateTitle('post.title')
    },
    methods: {
      loadData: (parameters) => {
        var _with = 'user,cover';
        parameters += "&_with=" + _with;
        return PostIndex(parameters);
      },
      handleCreate(items) {
        this.$router.push({ path: `/post/post/create` })
      },
      handleEdit(item) {
        this.$router.push({ path: `/post/post/${item.id}/edit` })
      },
      handleDelete(item) {
        DeleteConfirm(item.name, () => PostDestroy(item.id)).then(() => this.$refs.table.removeItem(item))
      },
      handleYes(item) {
        console.log('yes', item);
      },
      handleSkip(item) {
        console.log('skip', item);
      },
      handleConfirm(items) {
        console.log('confirm', items);
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
    .post-list-page {
        margin: 10px 30px;
        .el-table__body-wrapper {
            height: calc(100vh - 310px) !important;
        }
    }
</style>
