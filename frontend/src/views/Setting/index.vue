<template>
  <el-row class="setting-list-page list-page">
    <el-col :span="24">
      <h3 class="list-title">
        <span>{{$t('pages.system_setting.title')}}</span>
      </h3>
      <pre>{{settings}}</pre>
      <el-form label-position="right" label-width="160px" :rules="rules"
               :model="settings"
               ref="form" v-loading="loading">
        <form-item prop="name" :label="name('system','site_name')" :required="true">
          <el-input v-model="settings.site_name"></el-input>
        </form-item>
        <form-item prop="email" :label="name('system','site_introduction')" :required="true">
          <el-input v-model="settings.site_introduction" type="textarea"></el-input>
        </form-item>
        <form-item :label="name('system','default_language')">
          <el-select
            v-model="settings.default_language"
            value-key=""
            reserve-keyword>
            <el-option label="简体中文" value="zh-CN"/>
            <el-option label="English" value="en"/>
          </el-select>
        </form-item>

        <el-form-item :label="name('system','target_planets')">
          <el-checkbox-group v-model="settings.target_planets">
            <el-checkbox name="target_planets" label="earth">{{$t('setting.bundles.system.target_planets.earth')}}
            </el-checkbox>
            <el-checkbox name="target_planets" label="mars">{{$t('setting.bundles.system.target_planets.mars')}}
            </el-checkbox>
            <el-checkbox name="target_planets" label="sun">{{$t('setting.bundles.system.target_planets.sun')}}
            </el-checkbox>
            <el-checkbox name="target_planets" label="moon">{{$t('setting.bundles.system.target_planets.moon')}}
            </el-checkbox>
          </el-checkbox-group>
        </el-form-item>

        <form-item>
          <el-button type="primary" @click="handleSave">
            {{$t('global.terms.submit')}}
          </el-button>
          <el-button @click="handleDiscard">
            {{$t('pages.system_setting.discard')}}
          </el-button>
          <el-button @click="handleResetDefault">
            {{$t('pages.system_setting.reset')}}
          </el-button>
        </form-item>
      </el-form>
    </el-col>
  </el-row>
</template>

<script type="javascript">
  import setting_name from '@/utils/setting'
  import { SuccessMessage } from '@/utils/message'
  import { SettingIndex } from '@/api/setting'

  export default {
    components: {},
    mixins: [],
    computed: {},
    data() {
      let data = {
        rules: {},
        settings: {},
        loading: false
      };
      return data;
    },
    mounted() {
      this.loadData()
    },
    methods: {
      async loadData() {
        this.loading = true
        let result = await SettingIndex()
        this.settings = result.data
        this.loading = false
      },
      handleSave() {
      },
      handleDiscard() {
      },
      handleResetDefault() {
      },
      name(bundle, name) {
        return setting_name(bundle, name)
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
