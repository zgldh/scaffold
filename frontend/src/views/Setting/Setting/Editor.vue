<template>
  <el-row class="setting-editor-page" v-loading="loading">
    <el-col :span="11">
      <editor-title :name="$t('setting.title')"></editor-title>

      <el-form label-position="right" label-width="80px" :rules="rules" :model="form"
               ref="form">

        <form-item :label="$t('global.fields.id')" v-if="form.id">
          <el-input v-model="form.id" disabled></el-input>
        </form-item>
                    <form-item :label="$t('setting.fields.name')" prop="name">
              <el-input v-model="form.name"></el-input>
            </form-item>
            <form-item :label="$t('setting.fields.value')" prop="value">
              <el-input v-model="form.value"></el-input>
            </form-item>
            <form-item :label="$t('setting.fields.type')" prop="type">
              <el-input v-model="form.type"></el-input>
            </form-item>
            <form-item :label="$t('setting.fields.settable_id')" prop="settable_id">
              <el-input v-model="form.settable_id"></el-input>
            </form-item>
            <form-item :label="$t('setting.fields.settable_type')" prop="settable_type">
              <el-input v-model="form.settable_type"></el-input>
            </form-item>
        <form-item :label="$t('global.fields.created_at')" v-if="form.id">
          <el-input v-model="form.created_at" disabled></el-input>
        </form-item>

        <form-item>
          <el-button type="primary" @click="isCreating?onCreate():onUpdate()">
            {{$t('global.terms.submit')}}
          </el-button>
          <el-button @click="$router.go(-1)">{{$t('global.terms.back')}}</el-button>
        </form-item>
      </el-form>
    </el-col>
    <el-col :span="11" :offset="1"></el-col>
  </el-row>
</template>

<script type="javascript">
  import store  from '@/store'
  import { mapState } from 'vuex'
  import { SuccessMessage } from '@/utils/message'
  import { SettingStore, SettingUpdate, SettingShow } from '@/api/setting'
  import EditorMixin from '@/mixins/Editor'

  export default  {
    components: {},
    mixins: [EditorMixin],
    data () {
      return {
        rules: {},
        form: {
    "name": null,
    "value": "\"\"",
    "type": "system",
    "settable_id": null,
    "settable_type": null
}
      };
    },
    computed: {
      ...mapState({
//        items: state => state.model.items
      })
    },
//    beforeRouteEnter(to, from, next){
//      Preload store data here.
//      store.dispatch('user/LoadRoles').then(next);
//    },
    watch: {
      $route: 'fetchData',
    },
    mounted () {
      this.fetchData();
    },
    methods: {
      fetchData() {
        if (this.$route.params.id) {
          this.loading = true;
          SettingShow(this.$route.params.id, '_with=roles')
            .then(res => this.setFormData(res.data))
            .then(res => this.loading = false)
        }
      },
      setFormData(rawFormData){
        this.form = rawFormData
      },
      onCreate () {
        this.$refs.form.validate().then(valid => {
          this.loading = true;
          return SettingStore('_with=roles', this.form);
        })
          .then(SuccessMessage(this.$t('global.terms.save_completed')))
          .then(res => {
            this.loading = false;
            this.$router.replace({ path: `/setting/setting/${this.form.id}/edit` });
          })
          .catch(this.errorHandler);
      },
      onUpdate () {
        this.$refs.form.validate().then(valid => {
          this.loading = true;
          return SettingUpdate(this.form.id, '_with=roles', this.form)
        })
          .then(res => this.setFormData(res.data))
          .then(SuccessMessage(this.$t('global.terms.save_completed')))
          .then(res => this.loading = false)
          .catch(this.errorHandler);
      }
    }
  };
</script>

<style rel="stylesheet/scss" lang="scss">
  .setting-editor-page{
    margin: 10px 30px;
  }
</style>
