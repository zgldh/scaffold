<template>
    <el-row class="setting-list-page list-page">
        <el-col :span="24">
            <h3 class="list-title">
                <span>{{$t('pages.system_setting.title')}}</span>
            </h3>

            <pre>{{settings}}</pre>
        </el-col>
    </el-row>
</template>

<script type="javascript">
  import { SuccessMessage } from '@/utils/message'
  import { SettingIndex } from '@/api/setting'

  export default {
    components: {},
    mixins: [],
    computed: {},
    data() {
      let data = {
        settings: {}
      };
      return data;
    },
    mounted() {
      this.loadData()
    },
    methods: {
      async loadData() {
        let result = await SettingIndex()
        this.settings = result.data
      },
      handleSave() {
      },
      handleDiscard() {
      },
      handleResetDefault() {
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
