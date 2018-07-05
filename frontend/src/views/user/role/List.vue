<template>
  <el-row class="role-list-page" v-loading="loading">
    <el-col :span="24">
      <list-title :name="$t('role.title')"></list-title>

      <zgldh-datatables ref="table"
                        :source="loadData"
                        :actions="actions"
                        :multiple-actions="multipleActions"
                        :filters="advanceFilters"
                        :enable-selection="false"
      >
        <el-table-column
                prop="name"
                :label="$t('role.fields.name')"
                sortable="custom"
                show-overflow-tooltip
                width="180">
        </el-table-column>
        <el-table-column
                prop="label"
                :label="$t('role.fields.label')"
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
        <el-table-column
                prop="updated_at"
                :label="$t('global.fields.updated_at')"
                sortable="custom"
                searchable="false"
                show-overflow-tooltip>
        </el-table-column>
      </zgldh-datatables>
    </el-col>
  </el-row>
</template>

<script type="javascript">
  import { DeleteConfirm, Error422 } from '@/utils/message'
  import { RoleIndex, RoleDestroy, RoleCopy } from '@/api/user'
  import ListMixin from '@/mixins/List'
  import { mapState } from 'vuex'
  import { RoleCopyDialog } from '@/utils/permission'

  export default {
    components: {},
    mixins: [ListMixin],
    computed: {
      ...mapState({
        language: state => state.app.language,
      })
    },
    data (){
      let data = {
        loading: false,
        actions: [
          {
            Title: () => this.$i18n.t('global.terms.edit'),
            Handle: this.handleEdit
          },
          {
            Title: () => this.$i18n.t('global.terms.copy'),
            Handle: this.handleCopy
          },
          {
            Title: () => this.$i18n.t('global.terms.delete'),
            Handle: this.handleDelete
          },
          150
        ],
        multipleActions: [
          {
            Title: () => this.$i18n.t('global.terms.create'),
            Handle: this.handleCreate
          },
          // {
          //   Title: () => this.$i18n.t('global.terms.confirm'),
          //   More: true,
          //   TargetCare: true,
          //   Handle: this.handleConfirm
          // },
        ],
        advanceFilters: [
          {
            Name: () => this.$i18n.t('global.fields.created_at'),
            Field: 'created_at',
            Type: Date,
            Component: 'DateTimeRange'
          },
        ]
      };
      return data;
    },
    watch: {
      language(){
        this.$refs.table.loadSource();
      }
    },
    mounted(){
    },
    methods: {
      loadData (parameters) {
        parameters += "&_with=permissions";
        return RoleIndex(parameters)
      },
      handleEdit(item){
        this.$router.push({ path: `/user/role/${item.id}/edit` })
      },
      handleCopy(item){
        RoleCopyDialog(item).then(({ value }) => {
          this.loading = true
          return RoleCopy({
            id: item.id,
            name: value
          })
        }).then(result => {
          this.loading = false
          this.$refs.table.loadSource()
        }).catch(err => {
          this.loading = false
          Error422(err)
        });
      },
      handleDelete(item){
        DeleteConfirm(item.label, () => {
          this.loading = true
          return RoleDestroy(item.id)
        }).then(() => {
          this.loading = false
          this.$refs.table.removeItem(item)
        })
      },
      editPermission(item){
        this.$router.push({ path: `/user/role/${item.id}/permission-edit` })
      },
      handleSkip(item){
        console.log('skip', item);
      },
      handleCreate(items){
        this.$router.push({ path: `/user/role/create` })
      },
      handleConfirm(items){
        console.log('confirm', items);
      },
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  .role-list-page {
    margin: 10px 30px;
    .el-table__body-wrapper {
      height: calc(100vh - 310px) !important;
    }
  }
</style>
