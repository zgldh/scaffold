<template>
    <div class="reset-password-page">
        <el-form autoComplete="on" :model="resetForm" :rules="resetRules" ref="resetForm"
                 label-position="left" label-width="0px"
                 class="card-box main-form">
            <h3 class="title">{{$t('pages.password.reset_title')}}</h3>

            <form-item class="current-email">
                <span>{{resetForm.email}}</span>
            </form-item>

            <form-item prop="password">
                <span class="svg-container">
                  <svg-icon icon-class="password"></svg-icon>
                </span>
                <password-input name="password" v-model="resetForm.password"
                                :autofocus="true"
                                :placeholder="$t('pages.my_profile.new_password')"></password-input>
            </form-item>

            <form-item prop="password_confirmation">
                <span class="svg-container">
                  <svg-icon icon-class="password"></svg-icon>
                </span>
                <password-input name="password_confirmation"
                                v-model="resetForm.password_confirmation"
                                :placeholder="$t('pages.my_profile.repeat')"
                                @keyup.enter.native="handleReset"></password-input>
            </form-item>

            <form-item>
                <el-button type="primary" style="width:100%;" :loading="loading"
                           @click.native.prevent="handleReset">
                    {{$t('pages.password.reset_password')}}
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
  import { isvalidEmail, isvalidPassword } from '@/utils/validate'
  import LangSelect from '@/components/LangSelect'
  import PasswordInput from '@/components/PasswordInput'
  import { SuccessMessage, ErrorMessage } from "../../utils/message";

  export default {
    name: 'reset-page',
    components: {
      LangSelect,
      PasswordInput
    },
    data() {
      const validatePass = (rule, value, callback) => {
        if (!isvalidPassword(value)) {
          callback(new Error(this.$t('validation.min.string', {
            attribute: this.$t('pages.my_profile.new_password'),
            min: 6
          })))
        } else {
          if (this.resetForm.password_confirmation !== '') {
            this.$refs.resetForm.validateField('password_confirmation');
          }
          callback()
        }
      };
      const validatePass2 = (rule, value, callback) => {
        if (value === '') {
          callback(new Error(this.$t('validation.required', { attribute: this.$t('pages.my_profile.repeat') })));
        } else if (value !== this.resetForm.password) {
          callback(new Error(this.$t('validation.confirmed', {
            attribute: this.$t('user.fields.password')
          })));
        } else {
          callback();
        }
      };
      return {
        resetForm: {
          password_confirmation: '',
          password: '',
          token: '',
          email: this.$route.query.email
        },
        resetRules: {
          password: [{ required: true, trigger: 'blur', validator: validatePass }],
          password_confirmation: [{
            required: true,
            trigger: 'blur',
            validator: validatePass2
          }]
        },
        loading: false,
        pwdType: 'password'
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
      handleReset() {
        this.$refs.resetForm.validate(async valid => {
          if (valid) {
            this.loading = true;
            this.resetForm.token = this.$route.query.reset_token;
            this.resetForm.email = this.$route.query.email;
            try {
              await this.$store.dispatch('currentUser/Reset', this.resetForm)
              SuccessMessage(this.$t('pages.password.reset_success'))()
              this.$router.push({ name: 'login' })
            } catch (e) {
              ErrorMessage(this.$t('pages.password.reset_error'))()
            } finally {
              this.loading = false
            }
          }
        })
      }
    }
  }


</script>

<style rel="stylesheet/scss" lang="scss">
    @import "../../styles/variables";

    .reset-password-page {
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
        .current-email {
            text-align: center;
        }
    }
</style>
