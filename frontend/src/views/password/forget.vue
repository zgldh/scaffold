<template>
  <div class="send-password-reset-email-page">
    <el-form autoComplete="on" :model="forgetPassword" :rules="formRules"
             ref="forgetPassword"
             label-position="left" label-width="0px"
             class="card-box main-form">
      <h3 class="title">{{$t('app_name')}}</h3>

      <div class="tips">
        请输入您的注册邮箱，您将收到重置密码邮件。
      </div>
      <form-item prop="email">
        <i class="fa fa-envelope"></i>
        <el-input name="email" type="text" v-model="forgetPassword.email"
                  autoComplete="on" placeholder="email"/>
      </form-item>

      <form-item>
        <el-button type="primary" style="width:100%;" :loading="loading"
                   @click.native.prevent="sendResetEmail">
          {{$t('pages.password.send')}}
        </el-button>
        <el-button class="back-button" type="text"
                   @click="()=>{$router.push({name:'login'})}">
          {{$t('global.terms.back')}}
        </el-button>
      </form-item>
      <div class="tips" v-if="isSend">
        <span style="margin-right:20px;">{{$t('pages.password.send_email_success')}}</span>
      </div>
    </el-form>
  </div>
</template>

<script type="javascript">
  import { isvalidEmail } from '@/utils/validate'

  export default {
    name: 'login',
    data() {
      const validateEmail = (rule, value, callback) => {
        if (!isvalidEmail(value)) {
          callback(new Error('请输入正确的Email'))
        } else {
          callback()
        }
      }
      return {
        forgetPassword: {
          email: '',
          password: ''
        },
        formRules: {
          email: [{ required: true, trigger: 'blur', validator: validateEmail }]
        },
        loading: false,
        pwdType: 'password',
        isSend: false
      }
    },
    methods: {
      showPwd() {
        if (this.pwdType === 'password') {
          this.pwdType = ''
        } else {
          this.pwdType = 'password'
        }
      },
      sendResetEmail() {
        this.isSend = false;
        this.$refs.forgetPassword.validate(valid => {
          if (valid) {
            this.loading = true;
            this.$store.dispatch('currentUser/Forget', this.forgetPassword).then(() => {
              this.loading = false;
              this.isSend = true;
              this.$message({
                message: '重置邮件发送成功！',
                type: 'success'
              });
            }).catch(() => {
              this.loading = false
            })
          } else {
            console.log('error submit!!');
            return false
          }
        })
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss">
  @import "../../styles/variables";

  .send-password-reset-email-page {
    .back-button {
      display: block;
      margin: 0 auto;
      color: $borderL4;
    }
  }
</style>
