<template>
  <el-row class="my-profile-container">
    <el-col :span="11">
      <el-form ref="form" :model="form" label-width="120px">
        <h4>基本信息</h4>
        <form-item label="用户名">{{$store.state.currentUser.name}}</form-item>
        <form-item label="电子邮箱">{{$store.state.currentUser.email}}</form-item>
        <form-item label="手机">
          <inline-editor v-model="form.mobile" @change="onMobileChange"></inline-editor>
        </form-item>
        <form-item label="性别">
          <el-radio-group v-model="form.gender" @change="onGenderChange">
            <el-radio label="male">男</el-radio>
            <el-radio label="female">女</el-radio>
          </el-radio-group>
        </form-item>
        <form-item label="注册时间">{{$store.state.currentUser.createdAt}}</form-item>
      </el-form>
      <el-form ref="passwordForm" :model="passwordForm" :rules="passwordRules"
               label-width="120px">
        <h4>修改密码</h4>
        <form-item prop="oldPassword" label="旧密码">
          <el-input name="oldPassword" type="password"
                    v-model="passwordForm.oldPassword"></el-input>
        </form-item>
        <form-item prop="password" label="新密码">
          <el-input name="password" type="password"
                    v-model="passwordForm.password"></el-input>
        </form-item>
        <form-item prop="passwordRepeat" label="重复新密码">
          <el-input name="passwordRepeat" type="password"
                    v-model="passwordForm.passwordRepeat"></el-input>
        </form-item>
        <form-item>
          <el-button @click="onPasswordChange" :loading="passwordForm.loading">提交修改
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
    },
    data (){
      const validatePasswordRepeat = (rule, value, callback) => {
        return callback();
        if (this.passwordForm.password !== value) {
          callback(new Error('两次输入密码不一致'))
        } else {
          callback()
        }
      }
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
        passwordRules: {
          oldPassword: [
            { required: true, trigger: 'blur', message: '请填写密码' },
            { min: 5, trigger: 'blur', message: '密码不能小于5位' }
          ],
          password: [
            { required: true, trigger: 'blur', message: '请填写新密码' },
            { min: 5, trigger: 'blur', message: '新密码不能小于5位' }
          ],
          passwordRepeat: [
            { required: true, trigger: 'blur', message: '请重复新密码以加深记忆' },
            {
              required: true,
              trigger: 'blur',
              validator: validatePasswordRepeat.bind(this)
            }]
        }
      };
    },
    mounted(){
      this.form.mobile = this.$store.state.currentUser.mobile;
      this.form.gender = this.$store.state.currentUser.gender;
    },
    methods: {
      onMobileChange(newMobile){
        this.$store.dispatch('currentUser/UpdateCurrentUserMobile', newMobile)
                .then(SuccessMessage('手机更新完毕'))
                .catch(() => {
                })
      },
      onGenderChange(gender){
        this.$store.dispatch('currentUser/UpdateCurrentUserGender', gender)
                .then(SuccessMessage('性别更新完毕'))
                .catch(() => {
                })
      },
      onPasswordChange(){
        this.$refs.passwordForm.validate(valid => {
                  if (valid) {
                    this.passwordForm.loading = true;
                    this.$store.dispatch('currentUser/UpdateCurrentUserPassword', this.passwordForm)
                            .then((response) => {
                              this.passwordForm.loading = false;
                              this.passwordForm.oldPassword = '';
                              this.passwordForm.password = '';
                              this.passwordForm.passwordRepeat = '';
                              SuccessMessage('密码更新完毕')();
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
