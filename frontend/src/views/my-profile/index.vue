<template>
  <el-row class="my-profile-container">
    <el-col :span="11">
      <el-form ref="form" :model="form" label-width="120px">
        <h4>{{$t('pages.my_profile.basic')}}</h4>
        <form-item :label="$t('user.fields.name')">{{$store.state.currentUser.name}}
        </form-item>
        <form-item :label="$t('user.fields.email')">{{$store.state.currentUser.email}}
        </form-item>
        <form-item :label="$t('user.fields.mobile')">
          <inline-editor v-model="form.mobile" @change="onMobileChange"></inline-editor>
        </form-item>
        <form-item :label="$t('user.fields.gender')">
          <el-radio-group v-model="form.gender" @change="onGenderChange">
            <el-radio label="male">{{$t('user.terms.male')}}</el-radio>
            <el-radio label="female">{{$t('user.terms.female')}}</el-radio>
          </el-radio-group>
        </form-item>
        <form-item :label="$t('global.fields.created_at')">
          {{$store.state.currentUser.createdAt}}
        </form-item>
      </el-form>
      <el-form ref="passwordForm" :model="passwordForm" :rules="passwordRules"
               label-width="120px">
        <h4>{{$t('pages.my_profile.change_password')}}</h4>
        <form-item prop="oldPassword" :label="$t('pages.my_profile.old_password')">
          <el-input name="oldPassword" type="password"
                    v-model="passwordForm.oldPassword"></el-input>
        </form-item>
        <form-item prop="password" :label="$t('pages.my_profile.new_password')">
          <el-input name="password" type="password"
                    v-model="passwordForm.password"></el-input>
        </form-item>
        <form-item prop="passwordRepeat" :label="$t('pages.my_profile.repeat')">
          <el-input name="passwordRepeat" type="password"
                    v-model="passwordForm.passwordRepeat"></el-input>
        </form-item>
        <form-item>
          <el-button @click="onPasswordChange" :loading="passwordForm.loading">
            {{$t('global.terms.submit')}}
          </el-button>
        </form-item>
      </el-form>
    </el-col>
    <el-col :span="11" :offset="1">
      <avatar-editor :avatar="avatar"
                     @crop-upload-success="cropUploadSuccess">
      </avatar-editor>
    </el-col>
  </el-row>
</template>

<script type="javascript">
  import { mapState } from 'vuex'
  import InlineEditor from '@/components/InlineEditor'
  import AvatarEditor from '@/components/AvatarEditor'
  import { isvalidPassword } from '@/utils/validate'
  import { SuccessMessage } from '@/utils/message'
  import { getToken } from '@/utils/auth'
  import store from '@/store'

  export default {
    components: {
      InlineEditor,
      AvatarEditor
    },
    computed: {
      ...mapState({
        avatar: state => state.currentUser.avatar,
        roles: state => state.currentUser.roles,
        permissions: state => state.currentUser.permissions,
      }),
      passwordRules() {
        return {
          oldPassword: [
            {
              required: true,
              trigger: 'blur',
              message: this.$t('validation.required', { attribute: this.$t('pages.my_profile.old_password') })
            },
            {
              min: 5, trigger: 'blur',
              message: this.$t('validation.min.string', {
                attribute: this.$t('pages.my_profile.old_password'),
                min: 5
              })
            }
          ],
          password: [
            {
              required: true,
              trigger: 'blur',
              message: this.$t('validation.required', { attribute: this.$t('pages.my_profile.new_password') })
            },
            {
              min: 5, trigger: 'blur',
              message: this.$t('validation.min.string', {
                attribute: this.$t('pages.my_profile.new_password'),
                min: 5
              })
            }
          ],
          passwordRepeat: [
            {
              required: true, trigger: 'blur',
              message: this.$t('validation.confirmed', {
                attribute: this.$t('user.fields.password')
              })
            }
          ]
        }
      }
    },
    data (){
      return {
        form: {
          gender: '',
          mobile: '',
        },
        passwordForm: {
          oldPassword: '',
          password: '',
          passwordRepeat: '',
          loading: false
        },
      };
    },
    mounted(){
      this.form.mobile = this.$store.state.currentUser.mobile;
      this.form.gender = this.$store.state.currentUser.gender;
    },
    methods: {
      async onMobileChange(newMobile){
        await store.dispatch('currentUser/UpdateCurrentUserMobile', newMobile)
        SuccessMessage(this.$t('user.fields.mobile') + ' ' + this.$t('global.terms.save_completed'))()
      },
      async onGenderChange(gender){
        await store.dispatch('currentUser/UpdateCurrentUserGender', gender)
        SuccessMessage(this.$t('user.fields.gender') + ' ' + this.$t('global.terms.save_completed'))()
      },
      onPasswordChange(){
        this.$refs.passwordForm.validate(valid => {
                  if (valid) {
                    this.passwordForm.loading = true;
                    store.dispatch('currentUser/UpdateCurrentUserPassword', this.passwordForm)
                            .then((response) => {
                              this.passwordForm.loading = false;
                              this.passwordForm.oldPassword = '';
                              this.passwordForm.password = '';
                              this.passwordForm.passwordRepeat = '';
                              SuccessMessage(this.$t('user.fields.password') + ' ' + this.$t('global.terms.save_completed'))()
                            })
                            .catch(() => {
                              this.passwordForm.loading = false;
                            })
                  }
                }
        )
      },
      cropUploadSuccess({ jsonData, field }) {
        this.$store.commit('currentUser/SET_AVATAR', jsonData.data.url);
      },
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  .my-profile {
    &-container {
      margin: 10px 30px;
    }
    &-text {
      font-size: 30px;
      line-height: 46px;
    }
  }
</style>
