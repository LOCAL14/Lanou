<template>
    <div>
    <el-menu class="navbar" mode="horizontal">
        <hamburger class="hamburger-container" :toggleClick="toggleSideBar" :isActive="sidebar.opened"></hamburger>
        <breadcrumb></breadcrumb>
        <el-dropdown class="avatar-container" trigger="click" @command="handleCommand">
            <div class="avatar-wrapper">
                <img class="user-avatar" :src="avatar+'?imageView2/1/w/80/h/80'">
                <i class="el-icon-caret-bottom"></i>
            </div>
            <el-dropdown-menu class="user-dropdown" slot="dropdown" >
                <el-dropdown-item disabled>
                    {{name}}&nbsp;&nbsp;{{rolename}}{{city?'&nbsp;&nbsp;'+city:''}}
                </el-dropdown-item>
                <router-link class="inlineBlock" to="/">
                    <el-dropdown-item divided>
                        返回主页
                    </el-dropdown-item>
                </router-link>
                <el-dropdown-item divided command="set">
                    修改资料
                </el-dropdown-item>
                <el-dropdown-item divided>
                    <span @click="logout" style="display:block;">退出登录</span>
                </el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>


    </el-menu>
        <el-dialog title="修改资料" :visible.sync="dialogFormVisible">
            <el-form :model="form" status-icon style="width:60%;">
                <el-form-item label="用户名" label-width="70px">
                    <el-input v-model="form.username" disabled></el-input>
                </el-form-item>
                <el-form-item label="头像地址" label-width="70px">
                    <el-input v-model="form.avatarurl"></el-input>
                </el-form-item>
                <el-form-item label="重新上传" label-width="70px">
                    <el-upload
                            class="upload-demo"
                            action="https://iamxz-net.oss-cn-hangzhou.aliyuncs.com"
                            :data="uploadData"
                            list-type="picture"
                            :limit="1"
                            :before-upload="renameFile"
                            :on-success="getUrl">
                        <el-button size="small" type="primary">上传新头像</el-button>
                        <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb。
                            <br>重新上传会覆盖原来的图片地址</div>
                    </el-upload>
                </el-form-item>

            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="modifydetail">确 定</el-button>
            </div>

        </el-dialog>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import {modifydetail} from '@/api/adminuser'
    import Breadcrumb from '@/components/Breadcrumb'
    import Hamburger from '@/components/Hamburger'

    export default {
        components: {
            Breadcrumb,
            Hamburger
        },
        data() {
            return {
                form:{
                    username:'',
                    avatarurl:'',
                },
                filename: '',
                uploadData: {
                    'key': '',
                    'success_action_status': '200'
                },
                dialogFormVisible: false,
                formLabelWidth: '70px',
            }
        },
        created() {
            this.renameFile()
        },
        computed: {
            ...mapGetters([
                'sidebar',
                'avatar',
                'name',
                'city',
                'roles'
            ]),
            rolename: function () {
                switch (this.roles) {
                    case '0':
                        return '回收站员工';
                        break;
                    case '1':
                        return '客服';
                        break;
                    case '2':
                        return '地区运营';
                        break;
                    case '3':
                        return '中心运营';
                        break;
                    default:
                        return '欢迎你'
                }

            }
        },
        methods: {
            renameFile() {
                var timestamp = new Date().getTime();

                function makeid() {
                    var text = "";
                    var possible = "ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz";
                    for (var i = 0; i < 5; i++)
                        text += possible.charAt(Math.floor(Math.random() * possible.length));
                    return text;
                }

                this.filename = timestamp + makeid()
                this.uploadData.key = 'avatar/' + this.filename
                //命名规则：毫秒级时间戳+五位随机字母(无字母O)
            }, //上传OSS前重命名图片
            getUrl(res, file, fileList) {
                this.form.avatarurl = 'https://iamxz-net.oss-cn-hangzhou.aliyuncs.com//avatar/' + this.filename
            },
            handleCommand(command){
                if(command === 'set'){
                    this.set()
                }
            },
            set(){
                this.form.username = this.name
                this.form.avatarurl = this.avatar
                this.dialogFormVisible = true
            },
            toggleSideBar() {
                this.$store.dispatch('ToggleSideBar')
            },
            modifydetail() {
                var form = JSON.stringify(this.form)
                var that = this
                modifydetail(form).then(response => {
                    that.$store.dispatch('GetInfo').then(() => {
                        that.dialogFormVisible = false;
                    })
                })
            },
            logout() {
                this.$store.dispatch('LogOut').then(() => {
                    location.reload() // 为了重新实例化vue-router对象 避免bug
                })
            }
        }
    }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
    .navbar {
        height: 50px;
        line-height: 50px;
        border-radius: 0px !important;
        .hamburger-container {
            line-height: 58px;
            height: 50px;
            float: left;
            padding: 0 10px;
        }
        .screenfull {
            position: absolute;
            right: 90px;
            top: 16px;
            color: red;
        }
        .avatar-container {
            height: 50px;
            display: inline-block;
            position: absolute;
            right: 35px;
            .avatar-wrapper {
                cursor: pointer;
                margin-top: 5px;
                position: relative;
                .user-avatar {
                    width: 40px;
                    height: 40px;
                    border-radius: 10px;
                }
                .el-icon-caret-bottom {
                    position: absolute;
                    right: -20px;
                    top: 25px;
                    font-size: 12px;
                }
            }
        }
    }
</style>

