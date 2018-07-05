<template>
    <div class="login-container">
        <el-form autoComplete="on" :model="resetForm" :rules="resetRules" ref="resetForm"
                 label-position="left" label-width="0px"
                 class="card-box login-form">
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
                <el-input name="password_confirmation" :type="pwdType" @keyup.enter.native="handleReset"
                          v-model="resetForm.password_confirmation" autoComplete="on"
                          placeholder="confirm password"></el-input>
                <span class="show-pwd" @click="showPwd"><svg-icon icon-class="eye"/></span>
            </form-item>

            <form-item>
                <el-button type="primary" style="width:100%;" :loading="loading"
                           @click.native.prevent="handleReset">
                    {{$t('password.reset_password')}}
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
                    token:'',
                    email:''
                },
                resetRules: {
                    password: [{ required: true, trigger: 'blur', validator: validatePass }],
                    password_confirmation: [{ required: true, trigger: 'blur', validator: validatePass2 }]
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
    $bg: #2d3a4b;
    $dark_gray: #889aa4;
    $light_gray: #eee;

    .login-container {
        position: fixed;
        height: 100%;
        width: 100%;
        background-color: $bg;
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px #293444 inset !important;
            -webkit-text-fill-color: #fff !important;
        }
        input {
            background: transparent;
            border: 0px;
            -webkit-appearance: none;
            border-radius: 0px;
            padding: 12px 5px 12px 15px;
            color: $light_gray;
            height: 47px;
        }
        .el-input {
            display: inline-block;
            height: 47px;
            width: 85%;
        }
        .tips {
            font-size: 14px;
            color: #fff;
            margin-bottom: 10px;
        }
        .svg-container {
            padding: 6px 5px 6px 15px;
            color: $dark_gray;
            vertical-align: middle;
            width: 30px;
            display: inline-block;
            &_login {
                font-size: 20px;
            }
        }
        .title {
            font-size: 26px;
            font-weight: 400;
            color: $light_gray;
            margin: 0px auto 40px auto;
            text-align: center;
            font-weight: bold;
        }
        .login-form {
            position: absolute;
            left: 0;
            right: 0;
            width: 400px;
            padding: 35px 35px 15px 35px;
            margin: 120px auto;
        }
        .form-item {
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            color: #454545;
        }
        .show-pwd {
            position: absolute;
            right: 10px;
            top: 7px;
            font-size: 16px;
            color: $dark_gray;
            cursor: pointer;
            user-select: none;
        }
        .thirdparty-button {
            position: absolute;
            right: 35px;
            bottom: 28px;
        }
        i.fa.fa-envelope {
            padding: 6px 5px 6px 15px;
            color: $dark_gray;
            width: 30px;
        }
    }
</style>
