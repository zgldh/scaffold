<template>
    <div class="login-page">
        <el-form autoComplete="on" :model="loginForm" :rules="loginRules" ref="loginForm"
                 label-position="left" label-width="0px"
                 class="card-box main-form">
            <h3 class="title">{{$t('app_name')}}</h3>
            <form-item prop="email">
                <i class="fa fa-envelope"></i>
                <el-input name="email" type="text" v-model="loginForm.email"
                          :autofocus="true" autoComplete="on"
                          :placeholder="$t('user.fields.email')"/>
            </form-item>
            <form-item prop="password">
                <span class="svg-container">
                  <svg-icon icon-class="password"></svg-icon>
                </span>
                <password-input name="password"
                                v-model="loginForm.password"
                                :placeholder="$t('user.fields.password')"
                                @keyup.enter.native="handleLogin"></password-input>
            </form-item>
            <form-item>
                <el-button type="primary" style="width:100%;" :loading="loading"
                           @click.native.prevent="handleLogin">
                    {{$t('pages.login.sign_in')}}
                </el-button>
            </form-item>
            <el-row>
                <el-col :span="12">
                    <el-button class="tips" type="text" size="mini"
                               @click="forgetPassword">
                        {{$t('pages.login.forget_password')}}
                    </el-button>
                </el-col>
                <el-col :span="12">
                    <lang-select class="pull-right" theme="light"/>
                </el-col>
            </el-row>
        </el-form>
    </div>
</template>

<script type="javascript">
  import { isvalidEmail, isvalidPassword } from '@/utils/validate'
  import LangSelect from '@/components/LangSelect'
  import PasswordInput from '@/components/PasswordInput'

  export default {
    name: 'login',
    components: {
      LangSelect,
      PasswordInput
    },
    data() {
      const validateEmail = (rule, value, callback) => {
        if (!isvalidEmail(value)) {
          callback(new Error(this.$t('validation.email', { attribute: this.$t('user.fields.email') })))
        } else {
          callback()
        }
      }
      const validatePass = (rule, value, callback) => {
        if (!isvalidPassword(value)) {
          callback(new Error(this.$t('validation.min.string', {
            attribute: this.$t('user.fields.password'),
            min: 6
          })))
        } else {
          callback()
        }
      }
      return {
        loginForm: {
          email: '',
          password: ''
        },
        loginRules: {
          email: [{ required: true, trigger: 'blur', validator: validateEmail }],
          password: [{ required: true, trigger: 'blur', validator: validatePass }]
        },
        loading: false
      }
    },
    computed: {},
    methods: {
      handleLogin() {
        this.$refs.loginForm.validate(valid => {
          if (valid) {
            this.loading = true
            this.$store.dispatch('currentUser/Login', this.loginForm).then(() => {
              this.loading = false
              this.$router.push({ path: this.$route.query.redirect || '/' })
            }).catch(() => {
              this.loading = false
            })
          } else {
            console.log('error submit!!')
            return false
          }
        })
      },
      forgetPassword() {
        this.$router.push({ path: '/password/forget' })
      }
    }
  }


</script>

<style rel="stylesheet/scss" lang="scss">
    .login-page {
    }
</style>
