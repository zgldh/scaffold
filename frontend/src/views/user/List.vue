<template>
  <el-row class="user-list-page">
    <el-col :span="24">
      <list-title :name="$t('user.title')"></list-title>

      <zgldh-datatables ref="table"
                        :source="loadData"
                        :actions="actions"
                        :multiple-actions="multipleActions"
                        :filters="advanceFilters"
                        :title="$t('user.title')"
      >
        <el-table-column
                prop="name"
                :label="$t('user.fields.name')"
                sortable="custom"
                show-overflow-tooltip
                width="180">
        </el-table-column>
        <el-table-column
                prop="email"
                :label="$t('user.fields.email')"
                sortable="custom"
                show-overflow-tooltip>
        </el-table-column>
        <el-table-column
                prop="is_active"
                :label="$t('user.fields.is_active')"
                sortable="custom"
                searchable="false"
                show-overflow-tooltip>
          <template slot-scope="scope">
            {{scope.row.is_active?$t('global.terms.yes'):$t('global.terms.no')}}
          </template>
        </el-table-column>
        <el-table-column
                prop="last_login_at"
                :label="$t('user.fields.last_login_at')"
                sortable="custom"
                searchable="false"
                show-overflow-tooltip>
          <template slot-scope="scope">
            <el-tag v-if="scope.row.last_login_at">{{ scope.row.last_login_at }}
            </el-tag>
            <el-tag type="grey" v-if="scope.row.login_times">{{
              scope.row.login_times }}
            </el-tag>
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
          // {
          //   Title: () => this.$i18n.t('global.terms.yes'),
          //   More: true,
          //   Handle: this.handleYes
          // },
          // {
          //   Title: () => this.$i18n.t('global.terms.skip'),
          //   More: true,
          //   Handle: this.handleSkip
          // },
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
            Name: () => this.$i18n.t('user.fields.email'),
            Field: 'email',
            Type: String
          },
          {
            Name: () => this.$i18n.t('user.fields.gender'),
            Field: 'gender',
            Type: Boolean,
            ComponentParameters: {
              Multiple: true,
              Items: [
                {
                  Title: () => this.$i18n.t('user.terms.male'),
                  Value: "male"
                },
                {
                  Title: () => this.$i18n.t('user.terms.female'),
                  Value: "female"
                },
              ]
            }
          },
          {
            Name: () => this.$i18n.t('user.fields.is_active'),
            Field: 'is_active',
            Type: Boolean,
            ComponentParameters: {
              Multiple: false
            }
          },
          {
            Name: () => this.$i18n.t('user.fields.last_login_at'),
            Field: 'last_login_at',
            Type: Date,
            ComponentParameters: {
              Type: 'month',
              // DefaultTime: ['00:00:00', '23:59:59']
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
    mounted()
    {
    },
    methods: {
      loadData: UserIndex,
      handleCreate(items)
      {
        this.$router.push({ path: `/user/create` })
      },
      handleEdit(item)
      {
        this.$router.push({ path: `/user/${item.id}/edit` })
      },
      handleDelete(item)
      {
        DeleteConfirm(item.name, () => UserDestroy(item.id)).then(() => this.$refs.table.removeItem(item))
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
      },
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  .user-list-page {
    margin: 10px 30px;
    .el-table__body-wrapper {
      height: calc(100vh - 310px) !important;
    }
  }
</style>
