<template>
    <div class="admin-profile-page card bg-white">
        <div class="card-body">
            <el-form :show-message="true"
                     :rules="rules"
                     ref="form" :model="form"
                     label-width="150px"
                     label-position="left">
                <el-form-item class="required" :label="$t('global.login.name')" prop="name" :error="textFormat(errors.name)">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>

                <el-form-item class="required" :label="$t('user.fields.email')" prop="email" :error="textFormat(errors.email)">
                    <el-input v-model="form.email" type="email"></el-input>
                </el-form-item>

                <el-form-item class="required" :label="$t('user.fields.old_password')" prop="old_password" :error="textFormat(errors.old_password)">
                    <el-input v-model="form.old_password" :type="passwordShow.old_password ? 'text' : 'password'"></el-input>
                    <div :class="['eye-icon', 'ml-2', { 'show-password' : passwordShow.old_password }]" @click="toggleType('old_password')">
                        <i class="fa fa-eye"></i>
                        <i class="fa fa-eye-slash"></i>
                    </div>
                </el-form-item>

                <el-form-item class="required" :label="$t('user.fields.new_password')" prop="password" :error="textFormat(errors.password)">
                    <el-input v-model="form.password" :type="passwordShow.password ? 'text' : 'password'"></el-input>
                    <div :class="['eye-icon', 'ml-2', { 'show-password' : passwordShow.password }]" @click="toggleType('password')">
                        <i class="fa fa-eye"></i>
                        <i class="fa fa-eye-slash"></i>
                    </div>
                </el-form-item>

                <el-form-item class="required" :label="$t('user.fields.password_confirmation')" prop="password_confirmation" :error="textFormat(errors.password_confirmation)">
                    <el-input v-model="form.password_confirmation" :type="passwordShow.password_confirmation ? 'text' : 'password'"></el-input>
                    <div :class="['eye-icon', 'ml-2', { 'show-password' : passwordShow.password_confirmation }]" @click="toggleType('password_confirmation')">
                        <i class="fa fa-eye"></i>
                        <i class="fa fa-eye-slash"></i>
                    </div>
                </el-form-item>
            </el-form>
            <div class="buttons-footer">
                <div class="pull-left">
                    <md-button
                            type="button"
                            size="lg"
                            @click="onSave"
                            :title="$t('global.terms.save')"
                            :status="saving" variant="primary">
                    </md-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    import { loadModuleLanguage } from 'resources/assets/js/commons/LanguageHelper';

    export default {
        mixins: [
            loadModuleLanguage('user')
        ],
        data(){
            return {
                form: {
                    name: null,
                    email: null,
                    old_password: null,
                    password: null,
                    password_confirmation: null,
                },
                errors: {},
                saving: false,
                passwordShow: {
                    old_password: false,
                    password: false,
                    password_confirmation: false,
                },
                rules: {
                    old_password: [{ required: true, message: this.$t('user.fields.invalid_text', {
                        name: this.$t('user.fields.old_password')
                    }), trigger: 'blur' }],
                    password: [
                        { required: true, message: this.$t('user.fields.invalid_text', {
                            name: this.$t('user.fields.new_password')
                         }), trigger: 'blur' },
                        { min: 6, max:16, message: this.$t('user.fields.password_invalid_text'), trigger: 'blur' }],
                    password_confirmation: [{ required: true, message: this.$t('user.fields.invalid_text', {
                        name: this.$t('user.fields.password_confirmation')
                    }), trigger: 'blur' }]
                }
            }
        },
        computed : {
            currentUser : function(){
                return this.$store.state.currentUser;
            }
        },
        mounted(){
            this.initUser(this.currentUser);
        },
        methods: {
            initUser(data){
                this.form.name = data.name;
                this.form.email = data.email;
            },
            textFormat(val){
                return val ? val.toString().replace(/,/g,'') : '';
            },
            toggleType(type){
                this.passwordShow[type] = !this.passwordShow[type];
            },
            onSave(){
                this.$refs.form.validate(async (valid) => {
                    if(valid){
                        this.saving = true;
                        axios.put('user/password', this.form).then( () => {
                            this.saving = false;
                            this.$message({
                                type: 'success',
                                message: this.$i18n.t('global.terms.save_completed')
                            });
                        }).catch( res => {
                            this.saving = false;
                            this.errors = res.response.data.errors;
                            this.$message({
                                type: 'error',
                                message: res.response.data.message
                            });
                        })
                    }
                });
            }
        },
        watch:{
            currentUser : {
                handler : function(val){
                    this.initUser(val);
                },
                deep : true
            }
        }
    }
</script>

<style lang="scss">
    .admin-profile-page{
        .card-body{
            padding-left: 40px;
        }
        .buttons-footer{
            padding-left: 150px;
        }
        .el-form-item{
            .el-form-item__content{
                display: flex;
                .el-input{
                    width: 90%;
                }
                .eye-icon{
                    cursor: pointer;
                    .fa-eye{
                        display: none;
                    }
                    .fa-eye-slash{
                        display: inline-block;
                    }
                    &.show-password{
                        .fa-eye{
                            display: inline-block;
                        }
                        .fa-eye-slash{
                            display: none;
                        }
                    }
                }
            }
        }
    }
</style>