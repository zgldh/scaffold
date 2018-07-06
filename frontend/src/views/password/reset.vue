<template>
  <div class="reset-password-page">
    <el-form autoComplete="on" :model="resetForm" :rules="resetRules" ref="resetForm"
             label-position="left" label-width="0px"
             class="card-box main-form">
      <h3 class="title">{{$t('app_name')}}</h3>
      <el-input name="token" type="hidden"
                v-model="resetForm.token"></el-input>
      <el-input name="email" type="hidden"
                v-model="resetForm.email"></el-input>
      <form-item prop="password">
                <span class="svg-container">
                  <svg-icon icon-class="password"></svg-icon>
                </span>
        <el-input name="password" :type="pwdType"
                  v-model="resetForm.password" autoComplete="on"
                  placeholder="new password"></el-input>
        <span class="show-pwd" @click="showPwd"><svg-icon icon-class="eye"/></span>
      </form-item>

      <form-item prop="password_confirmation">
                <span class="svg-container">
                  <svg-icon icon-class="password"></svg-icon>
                </span>
        <el-input name="password_confirmation" :type="pwdType"
                  @keyup.enter.native="handleReset"
                  v-model="resetForm.password_confirmation" autoComplete="on"
                  placeholder="confirm password"></el-input>
        <span class="show-pwd" @click="showPwd"><svg-icon icon-class="eye"/></span>
      </form-item>

      <form-item>
        <el-button type="primary" style="width:100%;" :loading="loading"
                   @click.native.prevent="handleReset">
          {{$t('pages.password.reset_password')}}
        </el-button>
      </form-item>
    </el-form>
  </div>
</template>

<script type="javascript">

  export default {
    name: 'reset',
    data() {
      const validatePass = (rule, value, callback) => {
        if (value.length < 5) {
          callback(new Error('密码不能小于5位'))
        } else {
          if (this.resetForm.password_confirmation !== '') {
            this.$refs.resetForm.validateField('password_confirmation');
          }
          callback()
        }
      };
      const validatePass2 = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请再次输入密码'));
        } else if (value !== this.resetForm.password) {
          callback(new Error('两次输入密码不一致!'));
        } else {
          callback();
        }
      };
      return {
        resetForm: {
          password_confirmation: '',
          password: '',
          token: '',
          email: ''
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
    // beforeRouteEnter (to, from, next) {
    //     console.log(to,from);
    //     next(vm => {
    //       vm.token = to.query.reset_token;
    //       vm.email = to.query.email;
    //     });
    // },
    created () {
      console.log(this.$route);
      this.resetForm.token = this.$route.query.reset_token;
      this.resetForm.email = window.atob(this.$route.query.email);
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
        this.$refs.resetForm.validate(valid => {
          if (valid) {
            this.loading = true;
            this.$store.dispatch('currentUser/Reset', this.resetForm).then(() => {
              this.loading = false;
              this.$message({
                message: '重置成功!现在跳转至登陆页',
                type: 'success'
              });
              this.$router.push({ name: 'login' })
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
  .reset-password-page {
  }
</style>
