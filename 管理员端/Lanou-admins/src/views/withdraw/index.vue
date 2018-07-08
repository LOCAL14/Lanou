<template>
    <div class="app-container">
        <el-select v-model="listquery.title" placeholder="请选择搜索条件" class="select">
            <el-option label="提现订单ID" value="withdrawid"></el-option>
            <el-option label="手机号" value="phonenumber"></el-option>
        </el-select>
        <el-input placeholder="请输入搜索内容" v-model="listquery.value" class="input" :disabled="listquery.title?false:true">
            <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
        </el-input>
        <el-button icon="el-icon-refresh" @click="reset">重置</el-button>
        <div style="width:100%;display: block;height: 100%">
            <el-table :data="list" empty-text="暂无数据"
                      v-loading.body="listLoading" element-loading-text="加载数据中" border fit
                      highlight-current-row style="width: 100%" class="table-fixed el-table--scrollable-x">
                <el-table-column type="expand">
                    <template slot-scope="scope">
                        <el-form label-position="left" inline class="demo-table-expand">
                            <el-form-item label="订单备注" >
                                <span>{{ scope.row.remark }}</span>
                            </el-form-item>
                            <el-form-item label="错误代码" v-if="scope.row.error_code != 0">
                                <span>{{ scope.row.error_code }}</span>
                            </el-form-item>

                        </el-form>
                    </template>
                </el-table-column>
                <el-table-column align="center" label='ID' width="95">
                    <template slot-scope="scope">
                        {{scope.row.withdrawid}}
                    </template>
                </el-table-column>
                <el-table-column label="用户ID" width="110" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.userid}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="手机号" width="110" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.phonenumber}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="提现金额" width="110" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.withdraw_money}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="申请时间"  align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.apply_time}}</span>
                    </template>
                </el-table-column>
                <el-table-column class-name="status-col" label="状态" width="110" align="center">
                    <template slot-scope="scope">
                        <el-tag :type="scope.row.status | statusFilter">{{scope.row.status | statusReserve}}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" label="操作" width="220" fixed="right">
                    <template slot-scope="scope">
                        <el-button @click="toModifydetail(scope.row)" type="text" size="small">备注</el-button>
                        <el-button @click="modifyInfo(scope.row.withdrawid,'manual')" type="text" size="small">
                            人工提现
                        </el-button>
                        <el-button @click="modifyInfo(scope.row.withdrawid,'changestatus')" type="text" size="small"
                                   :disabled="scope.row.status == 1 ||scope.row.status == 3?false:true">
                            关闭
                        </el-button>
                        <el-button @click="modifyInfo(scope.row.withdrawid,'delete')" type="text" size="small">删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <el-pagination
                layout="total, prev, pager, next"
                :total="listTotal" :page-size="20" @current-change="changepages"
                :current-page.sync="currentPage">
        </el-pagination>

        <el-dialog title="添加备注" :visible.sync="dialogFormVisible">
            <el-form :model="temp">
                <el-form-item label="备注" :label-width="formLabelWidth">
                    <el-input v-model="temp.remark" type="textarea" :autosize="{ minRows: 3, maxRows: 5}"></el-input>
                </el-form-item>

            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" :loading="buttonLoading" @click="modifydetail()">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import {getList, modifyinfo,modifydetail} from '@/api/withdraw'
    import {mapGetters} from 'vuex'

    export default {
        data() {
            return {
                list: null,
                listTotal: 1,
                currentPage: 1,
                listLoading: true,
                listquery: {
                    page: 1,
                    limit: 20,
                    city: '',
                    title: '',
                    value: ''
                },
                listquerynow: {
                    page: '',
                    limit: '',
                    city: '',
                    title: '',
                    value: ''
                },
                temp:{
                },
                dialogFormVisible: false,
                formLabelWidth:'70px',
                buttonLoading:false
            }
        },
        computed: {
            ...mapGetters([
                'name',
                'city',
                'roles'
            ]),
        },
        filters: {
            statusFilter(status) {
                const statusMap = {
                    3: 'danger',
                    2: 'success',
                    1: 'warning',
                    0: 'info'
                }
                return statusMap[status]
            },
            statusReserve(status){
                const statusMap = {
                    3: '提现失败',
                    2: '提现完成',
                    1: '提现中',
                    0: '提现关闭'
                }
                return statusMap[status]
            },
        },
        created() {
            this.setQuerycity()
            this.fetchData(this.listquery)
        },
        methods: {
            checkroles(rolesmin) {
                if (this.roles < rolesmin) {
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
            setQuerycity() {
                if (this.city) {
                    this.listquery.city = this.city
                }
            },
            fetchData(query) {
                this.listLoading = true
                var listquery = JSON.stringify(query)

                getList(listquery).then(response => {
                    this.list = response.items
                    for(var item of this.list){
                        if (item.waste_detail) {
                            item.waste_detail = JSON.parse(item.waste_detail)
                        }
                    }
                    this.listTotal = response.total
                    this.listLoading = false
                    this.listquerynow.page = query.page
                    this.listquerynow.limit = query.limit
                    this.listquerynow.city = query.city
                    this.listquerynow.title = query.title
                    this.listquerynow.value = query.value
                    this.currentPage = 1
                })
            },
            reset() {
                this.listquery.city = this.city
                this.listquery.limit = 20
                this.listquery.title = ''
                this.listquery.value = ''
                this.fetchData(this.listquery)
            },
            search() {
                if (this.listquery.title) {
                    if (this.listquery.value) {
                        this.listquery.page = 1
                        this.fetchData(this.listquery)
                    } else {
                        this.$notify({
                            title: '错误',
                            message: '请输入搜索内容',
                            type: 'error',
                            duration: 2000
                        })
                    }

                } else {
                    this.$notify({
                        title: '错误',
                        message: '请选择搜索条件',
                        type: 'error',
                        duration: 2000
                    })
                }

            },
            toModifydetail(rowData){
                if (!this.checkroles(1)) {
                    return false
                }
                this.temp = Object.assign({}, rowData)
                this.dialogFormVisible = true

            },
            modifydetail(){
                const adminname = this.name
                this.buttonLoading = true
                var that = this
                var temp = JSON.stringify(this.temp)
                modifydetail(this.temp.withdrawid,temp, adminname).then(response => {
                    setTimeout(function () {
                        that.buttonLoading = false
                        that.dialogFormVisible = false

                    },300)
                    for (const v of this.list) {
                        if (v.withdrawid === this.temp.withdrawid) {
                            const index = this.list.indexOf(v)
                            this.list.splice(index, 1, this.temp)
                            break
                        }
                    }
                })
            },
            modifyInfo(id, type) {
                if (!this.checkroles(1)) { //todo 权限未设置
                    return false
                }
                const adminname = this.name
                modifyinfo(id, type, adminname).then(response => {
                    this.$notify({
                        title: '成功',
                        message: '操作成功',
                        type: 'success',
                        duration: 2000
                    })
                    this.fetchData(this.listquerynow)
                })

            },
            changepages(val) {
                this.listquerynow.page = val
                this.fetchData(this.listquerynow)

            }
        }


    }
</script>

<style rel="stylesheet/scss" lang="scss">
    .input {
        width: 50%;
    }

    .el-pagination {
        display: inline-block;
        float: left;

    }

    .table-fixed {
        margin: 10px 0;
        .demo-table-expand {
            font-size: 0;
        }
        .demo-table-expand label {
            width: 90px;
            color: #99a9bf;
        }
        .demo-table-expand .el-form-item {
            margin-right: 0;
            margin-bottom: 0;
            width: 50%;
        }
        /deep/ .el-table__fixed-right {
            height: 100% !important; //设置高优先，以覆盖内联样式
        }
        .detailtable{
            width:500px;
        }
        /deep/.detailtable th{
            border-right:0 !important;
        }
        /deep/.detailtable td{
            border-right:0 !important;
        }
    }
</style>