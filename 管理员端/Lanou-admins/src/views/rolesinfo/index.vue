<template>
    <el-container>
        <el-header height="80px" style=" background-color: #fff">
            <el-row type="flex"  justify="space-between">
                <el-col :span="6" class="logo-container">
                    <img src="https://ws4.sinaimg.cn/large/006tKfTcly1foggl6bzcfj305k05kmx6.jpg">
                    <p>管理员面板</p>
                </el-col>
                <el-col :span="6" >
                    <el-menu mode="horizontal" :default-active="activeIndex" router="true">
                        <el-menu-item index="1" class="menu-item">工作面板</el-menu-item>
                        <el-menu-item index="2" class="menu-item" disabled>权限说明</el-menu-item>
                        <el-menu-item index="3" class="menu-item" disabled>帮助</el-menu-item>
                    </el-menu>
                </el-col>
            </el-row>
        </el-header>
        <el-main class="main">

        </el-main>
        <el-footer>
            <p class="copyright">
                WALL·E版权所有 © 2018&nbsp;&nbsp;·&nbsp;&nbsp;黑ICP备17009385号-2
            </p>
        </el-footer>
    </el-container>

</template>

<script>
    import {isvalidUsername} from '@/utils/validate'

    export default {
        name: 'login',
        data() {
            const validateUsername = (rule, value, callback) => {
                if (value.length < 5) {
                    callback(new Error('用户名不能小于5位'))
                } else {
                    callback()
                }
            }
            const validatePass = (rule, value, callback) => {
                if (value.length < 5) {
                    callback(new Error('密码不能小于5位'))
                } else {
                    callback()
                }
            }
            return {
                activeIndex:'2',
                loginForm: {
                    username: 'testadmin',
                    password: 'testadmin'
                },
                loginRules: {
                    username: [{required: true, trigger: 'blur', validator: validateUsername}],
                    password: [{required: true, trigger: 'blur', validator: validatePass}]
                },
                loading: false,
                pwdType: 'password',
                carousel_items:['https://ws2.sinaimg.cn/large/006tKfTcly1fo95ek7wzsj31kw120go5.jpg',
                                'https://ws1.sinaimg.cn/large/006tKfTcly1fo96c29l1ej31kw120wh8.jpg']
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
            handleLogin() {
                this.$refs.loginForm.validate(valid => {
                    if (valid) {
                        this.loading = true
                        this.$store.dispatch('Login', this.loginForm).then(() => {
                            this.loading = false
                            this.$router.push({path: '/'})
                        }).catch(() => {
                            this.loading = false
                        })
                    } else {
                        console.log('error submit!!')
                        return false
                    }
                })
            }
        }
    }
</script>

<style rel="stylesheet/scss" lang="scss" >
    $bg: #2d3a4b;
    $dark_gray: #889aa4;
    $light_gray: #eee;
    $lanou-green:#2DCB70;

    .logo-container{
        display: flex;
        align-items: center;

        img {
            height:80px;
            margin-left:40px;
        }
        p{
            margin:0 0 0 20px;
            font-size:24px;
            font-family: "Microsoft YaHei";
            color: $dark_gray;
        }
    }

    .el-menu::after {
        clear: none;

    }

    .el-menu--horizontal{
        border: 0;

    }




    .main {
        margin: 0px;
        padding: 20px;
        background-color: #f5f7fa;

        .login-container {

            input:-webkit-autofill {
                -webkit-box-shadow: 0 0 0px 1000px #293444 inset !important;
                -webkit-text-fill-color: #fff !important;
            }
            input {
                border: 0;
                -webkit-appearance: none;
                border-radius: 0;
                padding: 12px 5px 12px 15px;
                /*<!--color: $light_gray;-->*/
                height: 47px;
            }
            .el-input {
                display: inline-block;
                height: 47px;
                width: 85%;
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
                color: $dark_gray;
                margin-left: 10px;
                font-weight: bold;
            }
            .login-form {
                left: 0;
                right: 0;
                width: 400px;
                padding: 35px;
                margin: 0px auto;
            }
            .el-form-item {
                border: 1px solid $lanou-green;
                border-radius: 5px;
                color: #454545;
                background-color: #fff;
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
                /*position: absolute;*/
                right: 35px;
                bottom: 28px;
            }
        }
        .carousel {
            width: 90%;
            margin: 10px auto;
            .el-carousel__item {
                background-color: #99a9bf;
                border-radius: 10px;
                img {
                    width: 100%;

                }
            }
        }
    }

    .copyright {
        color:$dark_gray;
        margin: 60px auto 0 auto;
        font-size: 14px;
        text-align: center;
    }


</style>
