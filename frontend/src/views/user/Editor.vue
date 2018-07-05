<template>
  <el-row class="user-editor-page" v-loading="loading">
    <el-col :span="11">
      <editor-title :name="$t('user.title')"></editor-title>

      <el-form label-position="right" label-width="80px" :rules="rules" :model="form"
               ref="form">
        <form-item prop="name" :label="$t('user.fields.name')" :required="true">
          <el-input v-model="form.name"></el-input>
        </form-item>
        <form-item prop="email" :label="$t('user.fields.email')">
          <el-input v-model="form.email"></el-input>
        </form-item>

        <form-item prop="password" :label="$t('user.fields.password')"
                   :required="isCreating">
          <el-input type="password" v-model="form.password"
                    auto-complete="off"></el-input>
          <span v-if="isCreating == false">不想修改密码请留空</span>
        </form-item>

        <form-item :label="$t('user.fields.is_active')">
          <el-switch v-model="form.is_active"></el-switch>
        </form-item>
        <form-item :label="$t('role.title')">
          <el-select
                  v-model="form.roles"
                  value-key=""
                  multiple
                  filterable
                  reserve-keyword
                  placeholder="请输入关键词">
            <el-option
                    v-for="item in roles"
                    :key="item.name"
                    :label="item.name"
                    :value="item.id">
            </el-option>
          </el-select>
        </form-item>
        <form-item>
          <el-button type="primary" @click="isCreating?onCreate():onUpdate()">
            {{$t('global.terms.submit')}}
          </el-button>
          <el-button @click="$router.go(-1)">{{$t('global.terms.back')}}</el-button>
        </form-item>
      </el-form>
    </el-col>
    <el-col :span="11" :offset="1">
      <avatar-editor :avatar="form.avatar_url||''" :user-id="this.form.id"
                     @crop-upload-success="cropUploadSuccess">
      </avatar-editor>
    </el-col>
  </el-row>
</template>

<script type="javascript">
  import store  from '@/store'
  import { mapState } from 'vuex'
  import AvatarEditor from '@/components/AvatarEditor'
  import { SuccessMessage } from '@/utils/message'
  import { UserStore, UserUpdate, UserShow, RoleIndex } from '@/api/user'
  import EditorMixin from '@/mixins/Editor'

  export default {
    components: { AvatarEditor },
    mixins: [EditorMixin],
    data () {
      return {
        rules: {
          email: {
            required: true,
            type: 'email',
            trigger: ['blur', 'change']
          },
        },
        form: {
          name: '',
          email: '',
          password: '',
          is_active: true,
          roles: []
        }
      };
    },
    computed: {
      ...mapState({
        roles: state => state.user.roles
      })
    },
    beforeRouteEnter(to, from, next){
      store.dispatch('user/LoadRoles').then(next);
    },
    mounted () {
      this.fetchData();
    },
    watch: {
      $route: 'fetchData',
    },
    methods: {
      fetchData() {
        if (this.$route.params.id) {
          this.loading = true;
          UserShow(this.$route.params.id, '_with=roles')
                  .then(res => this.setFormData(res.data))
                  .then(res => this.loading = false)
        }
      },
      setFormData(rawFormData){
        this.form = rawFormData
        this.form.roles = this.form.roles.map(item => item.id)
      },
      onCreate () {
        this.$refs.form.validate().then(valid => {
          this.loading = true;
          return UserStore('_with=roles', this.form);
        })
                .then(res => this.setFormData(res.data))
                .then(SuccessMessage(this.$t('global.terms.save_completed')))
                .then(res => {
                  this.loading = false;
                  this.$router.replace({ path: `/user/${this.form.id}/edit` });
                })
                .catch(this.errorHandler);
      },
      onUpdate () {
        this.$refs.form.validate().then(valid => {
          this.loading = true;
          return UserUpdate(this.form.id, '_with=roles', this.form)
        })
                .then(res => this.setFormData(res.data))
                .then(SuccessMessage(this.$t('global.terms.save_completed')))
                .then(res => this.loading = false)
                .catch(this.errorHandler);
      },
      cropUploadSuccess({ jsonData, field }) {
        this.form.avatar_url = jsonData.data.url;
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  .user-editor-page {
    margin: 10px 30px;
  }
</style>
