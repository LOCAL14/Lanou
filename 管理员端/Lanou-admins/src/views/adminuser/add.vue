<template>
    <div class="app-container">
        <h3>添加管理员</h3>
        <h4 v-if="roles < 2">抱歉，你的操作权限不足</h4>
        <div class="form-container" v-if="roles >= 2">
            <el-form ref="form" :model="form" label-width="120px">
                <h4>账号设置</h4>
                <el-form-item label="用户名"  >
                    <el-input v-model="form.username" style="width:90%;"></el-input>
                </el-form-item>
                <el-form-item label="密码"  >
                    <el-input v-model="form.password" style="width:90%;"></el-input>
                </el-form-item>

                <h4>权限设置</h4>
                <el-form-item label="权限">
                    <el-radio-group v-model="form.role" @change="roleChange">
                        <el-radio :label="0">回收站员工</el-radio>
                        <el-radio :label="1">客服</el-radio>
                        <el-radio :label="2">城市运营</el-radio>
                        <el-radio :label="3" :disabled="roles == 3 ?false:true ">中心运营</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="分管区域">
                    <el-cascader
                            :options="cityOptions"
                            :value="citySelected"
                            :placeholder="form.role == 0 ||form.role == 2 ?'可搜索区名':'此权限无需设置区域'"
                            @change="handleCityChange"
                            filterable
                            :disabled="form.role == 0 ||form.role == 2 ?false:true "
                    ></el-cascader>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="onSubmit" :loading="setbuttonLoading">确认添加</el-button>
                    <el-button @click="onCancel">取消</el-button>
                </el-form-item>
            </el-form>
        </div>


    </div>
</template>

<script>
    import {newAdminuser} from "../../api/adminuser";
    import citydata from "../../components/citydata/citydata";
    import {mapGetters} from 'vuex'

    export default {
        data() {
            return {
                cityOptions: citydata,
                citySelected:[],
                form: {
                    username:'',
                    password:'',
                    role: 0,
                    city:'',
                },
                setbuttonLoading: false,


            }
        },
        computed: {
            ...mapGetters([
                'name',
                'city',
                'roles'
            ]),
        },
        created() {

        },
        methods: {
            roleChange(label){
                console.log(label)
                if(label === 1 || label === 3){
                    this.citySelected = []
                    this.form.city = ''
                }
            },
            handleCityChange(array) {
                if (!array[2]) {
                    //直辖市
                    this.form.city = array[0]
                } else {
                    this.form.city = array[1]
                }
            },

            onSubmit() {
                for (var key in this.form) {
                    if (this.form[key] === '') {
                        var words
                        switch (key) {
                            case 'city' :
                                if(this.form.role === 0 || this.form.role === 2){
                                    words = '未选择管辖区域'
                                    break;
                                }else{
                                    continue
                                }
                            case 'username' :
                                words = '未输入用户名'
                                break;
                            case 'password' :
                                words = '未输入密码'
                                break;
                            default:
                                words = '表单填写不完整'
                        }
                        this.$notify({
                            title: '错误',
                            message: words,
                            type: 'error',
                            duration: 2000
                        })
                        return false
                    }

                } //检验表单完整性
                var form = JSON.stringify(this.form)
                this.setbuttonLoading = true
                newAdminuser(form, this.name).then(response => {
                    if(response === '用户名重复'){
                        this.$notify({
                            title: '错误',
                            message: '用户名重复',
                            type: 'error',
                            duration: 2000
                        })
                        this.setbuttonLoading = false
                    }else{
                        this.setbuttonLoading = false
                        this.$router.push({path: '/adminuser/adminuserlist'})
                    }
                })
            },
            onCancel() {
                this.$router.push({path: '/'})
            }
        }
    }
</script>

<style scoped>
    h3 {
        margin: 0 0 30px 5px;

    }

    h4 {
        margin: 10px;
        color:#99a9bf
    }

    .form-container {
        width: 60%;
        padding-left: 10px;

    }
</style>

