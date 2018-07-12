<template>
    <div class="send-password-reset-email-page">
        <el-form autoComplete="on" :model="forgetPassword" :rules="formRules"
                 ref="forgetPassword"
                 @submit.native.prevent="sendResetEmail"
                 label-position="left" label-width="0px"
                 class="card-box main-form">
            <h3 class="title">{{$t('pages.password.forget_title')}}</h3>

            <div class="tips">{{$t('pages.password.forget_note')}}</div>

            <form-item prop="email">
                <i class="fa fa-envelope"></i>
                <el-input name="email" type="text" v-model="forgetPassword.email"
                          :autofocus="true" autoComplete="on" placeholder="email"/>
            </form-item>


            <form-item>
                <el-alert v-if="isSend" :title="$t('pages.password.send_email_success')"
                          type="success"
                          :closable="false"
                          show-icon>
                </el-alert>
                <el-button v-else type="primary" style="width:100%;"
                           :loading="loading"
                           @click.native.prevent="sendResetEmail">
                    {{$t('pages.password.forget_send')}}
                </el-button>
            </form-item>
            <el-row>
                <el-col :span="24">
                    <el-button class="back-button tips" type="text" size="mini"
                               @click="()=>{$router.push({name:'login'})}">
                        {{$t('pages.password.back_to_login')}}
                    </el-button>
                    <lang-select theme="light"/>
                </el-col>
            </el-row>
        </el-form>
    </div>
</template>

<script type="javascript">
  import LangSelect from '@/components/LangSelect'
  import { isvalidEmail } from '@/utils/validate'

  export default {
    name: 'forget-page',
    components: {
      LangSelect
    },
    data() {
      const validateEmail = (rule, value, callback) => {
        if (!isvalidEmail(value)) {
          callback(new Error(this.$t('validation.email', { attribute: value })))
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
        .lang-select {
            position: absolute;
            right: 0;
            top: 4px;
        }
    }
</style>
