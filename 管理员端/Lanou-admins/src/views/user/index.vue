<template>
    <div class="app-container">
        <el-select v-model="listquery.title" placeholder="请选择搜索条件" class="select">
            <el-option label="手机号" value="phonenumber"></el-option>
            <el-option label="小区名" value="neighborhood"></el-option>
        </el-select>
        <el-input placeholder="请输入搜索内容" v-model="listquery.value" class="input" :disabled="listquery.title?false:true">
            <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
        </el-input>
        <el-button icon="el-icon-refresh" @click="reset">重置</el-button>
        <div style="width:100%;display: block;height: 100%">
        <el-table :data="roles >= 1?list:''" :empty-text = "roles >= 1?'暂无数据':'你无权访问此数据'"
                  v-loading.body="listLoading" element-loading-text="数据加载中" border fit
                  highlight-current-row style="width: 100%" class="table-fixed el-table--scrollable-x">
            <el-table-column align="center" label='ID' width="95" fixed="left">
                <template slot-scope="scope">
                    {{scope.row.userid}}
                </template>
            </el-table-column>
            <el-table-column label="手机号" width="110" align="center">
                <template slot-scope="scope">
                    <span>{{scope.row.phonenumber}}</span>
                </template>
            </el-table-column>
            <el-table-column label="微信昵称" width="110" align="center">
                <template slot-scope="scope">
                    {{scope.row.wxnickname}}
                </template>
            </el-table-column>
            <el-table-column align="center" prop="created_at" label="注册时间" width="200">
                <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span>{{scope.row.registertime}}</span>
                </template>
            </el-table-column>
            <el-table-column align="center" label="信用积分" width="110">
                <template slot-scope="scope">
                    {{scope.row.creditscore}}
                </template>
            </el-table-column>
            <el-table-column align="center" label="余额" width="110">
                <template slot-scope="scope">
                    {{scope.row.money}}
                </template>
            </el-table-column>
            <el-table-column align="center" label="城市" width="200">
                <template slot-scope="scope">
                    {{scope.row.city}}
                </template>
            </el-table-column>
            <el-table-column align="center" label="小区" width="200">
                <template slot-scope="scope">
                    {{scope.row.neighborhood}}
                </template>
            </el-table-column>
            <el-table-column class-name="status-col" label="用户状态" width="110" align="center" >
                <template slot-scope="scope">
                    <el-tag :type="scope.row.status | statusFilter">{{scope.row.status === '0'?'停用':'正常'}}</el-tag>
                </template>
            </el-table-column>
            <el-table-column align="center" label="操作" width="100" fixed="right">
                <template slot-scope="scope">
                    <el-button @click="modifyInfo(scope.row.userid,'changestatus')" type="text" size="small">
                        {{scope.row.status === '0'?'恢复':'停用'}}</el-button>
                    <el-button @click="modifyInfo(scope.row.userid,'delete')" type="text" size="small">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        </div>
            <el-pagination
                    layout="total, prev, pager, next"
                    :total="listTotal" :page-size="20" @current-change="changepages"
                    :current-page.sync="currentPage">
            </el-pagination>
    </div>
</template>

<script>
    import {getList,modifyinfo} from '@/api/user'
    import { mapGetters } from 'vuex'

    export default {
        data() {
            return {
                list: null,
                listTotal: 1,
                currentPage:1,
                listLoading: true,
                listquery:{
                    page: 1,
                    limit: 20,
                    city:'',
                    title: '',
                    value: ''
                },
                listquerynow:{
                    page: '',
                    limit: '',
                    city:'',
                    title: '',
                    value: ''
                }
            }
        },
        computed:{
            ...mapGetters([
                'name',
                'city',
                'roles'
            ]),
        },
        filters: {
            statusFilter(status) {
                const statusMap = {
                    1: 'success',
                    0: 'danger'
                }
                return statusMap[status]
            }
        },
        created() {
            this.setQuerycity()
            this.fetchData(this.listquery)
            this.currentPage = 1
        },
        methods: {
            checkroles(rolesmin){
                if(this.roles < rolesmin){
                    this.$notify({
                        title: '权限不足',
                        message: '你无权进行此操作',
                        type: 'error',
                        duration: 2000
                    })
                    return false
                }
                return true
            },
            setQuerycity(){
                if(this.city){
                    this.listquery.city = this.city
                }
            },
            fetchData(query) {
                this.listLoading = true
                var listquery = JSON.stringify(query)
                getList(listquery).then(response => {
                    this.list = response.items
                    this.listTotal = response.total
                    this.listLoading = false
                    this.listquerynow.page = query.page
                    this.listquerynow.limit = query.limit
                    this.listquerynow.city = query.city
                    this.listquerynow.title = query.title
                    this.listquerynow.value = query.value

                })

            },
            reset(){
                this.listquery.city =this.city
                this.listquery.limit= 20
                this.listquery.title= ''
                this.listquery.value= ''
                this.fetchData(this.listquery)
                this.currentPage = 1

            },
            search(){
                if(this.listquery.title){
                    if(this.listquery.value){
                        this.listquery.page = 1
                        this.fetchData(this.listquery)
                    }else{
                        this.$notify({
                            title: '错误',
                            message: '请输入搜索内容',
                            type: 'error',
                            duration: 2000
                        })
                    }

                }else{
                    this.$notify({
                        title: '错误',
                        message: '请选择搜索条件',
                        type: 'error',
                        duration: 2000
                    })
                }

            },
            modifyInfo(id,type){
                if(!this.checkroles(2)){
                    return false
                }
                const adminname = this.name
                modifyinfo(id,type,adminname).then(response => {
                    this.$notify({
                        title: '成功',
                        message: '操作成功',
                        type: 'success',
                        duration: 2000
                    })
                    this.fetchData(this.listquerynow)
                    this.currentPage = 1
                })

            },
            changepages(val){
               this.listquerynow.page = val
                this.fetchData(this.listquerynow)

            }
        }


    }
</script>


<style rel="stylesheet/scss" lang="scss">
    .input{
         width:50%;
     }
   .el-pagination{
       display: inline-block;
       float: left;

   }
   .table-fixed {
       margin:10px 0;
       /deep/ .el-table__fixed-right {
           height: 100% !important; //设置高优先，以覆盖内联样式
       }
   }
</style>