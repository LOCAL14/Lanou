<template>
    <div class="app-container">
        <el-select v-model="listquery.title" placeholder="请选择搜索条件" class="select">
            <el-option label="姓名" value="name"></el-option>
            <el-option label="手机号" value="phonenumber"></el-option>
            <el-option label="小区名" value="neighborhood"></el-option>
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
                            <el-form-item label="用户ID" >
                                <span>{{ scope.row.userid }}</span>
                            </el-form-item>
                            <el-form-item label="回收员ID">
                                <span>{{ scope.row.collectorid }}</span>
                            </el-form-item>
                            <el-form-item label="楼号">
                                <span>{{ scope.row.address3 }}</span>
                            </el-form-item>
                            <el-form-item label="详细地址">
                                <span>{{ scope.row.address4 }}</span>
                            </el-form-item>
                            <el-form-item label="预约日期" v-if="scope.row.appoint_date != 0">
                                <span>{{ scope.row.appoint_date }}</span>
                            </el-form-item>
                            <el-form-item label="预约时间" v-if="scope.row.appoint_time != 0">
                                <span>{{ scope.row.appoint_time | appointtimeFilter }}</span>
                            </el-form-item>
                            <el-form-item label="预估重量" v-if="scope.row.evaluate_weight">
                                <span>{{ scope.row.evaluate_weight }}</span>
                            </el-form-item>
                            <el-form-item label="预估种类" v-if="scope.row.evaluate_type">
                                <span>{{ scope.row.evaluate_type }}</span>
                            </el-form-item>
                            <el-form-item label="取货时间" v-if="scope.row.take_time != 0">
                                <span>{{ scope.row.take_time }}</span>
                            </el-form-item>
                            <el-form-item label="送货时间" v-if="scope.row.send_time != 0">
                                <span>{{ scope.row.send_time }}</span>
                            </el-form-item>
                            <el-form-item label="评价星级" v-if="scope.row.rate_star != 0">
                                <span>{{ scope.row.rate_star }}</span>
                            </el-form-item>
                            <el-form-item label="评价内容" v-if="scope.row.rate_text">
                                <span>{{ scope.row.rate_text }}</span>
                            </el-form-item>
                            <el-form-item label="投诉内容" v-if="scope.row.complaint_text">
                                <span>{{ scope.row.complaint_text }}</span>
                            </el-form-item>
                            <el-form-item label="投诉时间" v-if="scope.row.complaint_time != 0">
                                <span>{{ scope.row.complaint_time }}</span>
                            </el-form-item>
                            <el-form-item label="订单金额" v-if="scope.row.order_money">
                                <span>{{ scope.row.order_money }}</span>
                            </el-form-item>
                        </el-form>
                        <p v-if="scope.row.waste_detail" style="color:#99a9bf">回收详情</p>
                        <el-table :data="scope.row.waste_detail"  v-if="scope.row.waste_detail"
                                  border class="detailtable">
                            <el-table-column align="center" label='类别' width="95">
                                <template slot-scope="scope">
                                    {{scope.row[0]}}
                                </template>
                            </el-table-column>
                            <el-table-column align="center" label='下单单价' width="95">
                                <template slot-scope="scope">
                                    {{scope.row[1]}}
                                </template>
                            </el-table-column>
                            <el-table-column align="center" label='数量/重量' width="95">
                                <template slot-scope="scope">
                                    {{scope.row[2]}}
                                </template>
                            </el-table-column>
                            <el-table-column align="center" label='总价'>
                                <template slot-scope="scope">
                                    {{scope.row[3]}}
                                </template>
                            </el-table-column>
                        </el-table>
                    </template>
                </el-table-column>
                <el-table-column align="center" label='ID' width="95">
                    <template slot-scope="scope">
                        {{scope.row.orderid}}
                    </template>
                </el-table-column>
                <el-table-column label="联系人" width="110" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.contact_name}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="手机号" width="110" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.phonenumber}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="城市" width="110" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.address5}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="小区" width="110" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.address2}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="回收员" width="110" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.collector_name}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="回收员电话" width="110" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.collector_phonenumber}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="下单时间" width="200" align="center">
                    <template slot-scope="scope">
                        {{scope.row.order_time}}
                    </template>
                </el-table-column>
                <el-table-column class-name="status-col" label="状态" width="110" align="center">
                    <template slot-scope="scope">
                        <el-tag :type="scope.row.status | statusFilter">{{scope.row.status | statusReserve}}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" label="操作" width="180" fixed="right">
                    <template slot-scope="scope">
                        <el-button @click="toModifydetail(scope.row)" type="text" size="small">修改</el-button>
                        <el-button @click="modifyInfo(scope.row.orderid,'changecollector')" type="text" size="small">
                            改派
                        </el-button>
                        <el-button @click="modifyInfo(scope.row.orderid,'changestatus')" type="text" size="small"
                                   :disabled="scope.row.status === '0'?true:false">
                            关闭
                        </el-button>
                        <el-button @click="modifyInfo(scope.row.orderid,'delete')" type="text" size="small">删除
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

        <el-dialog title="修改信息" :visible.sync="dialogFormVisible">
            <el-form :model="temp">
                <el-form-item label="联系人" :label-width="formLabelWidth">
                    <el-input v-model="temp.contact_name"></el-input>
                </el-form-item>
                <el-form-item label="手机号" :label-width="formLabelWidth">
                    <el-input v-model="temp.phonenumber" ></el-input>
                </el-form-item>
                <el-form-item label="城市" :label-width="formLabelWidth">
                    <el-input v-model="temp.address5" ></el-input>
                </el-form-item>
                <el-form-item label="小区" :label-width="formLabelWidth">
                    <el-input v-model="temp.address2" ></el-input>
                </el-form-item>
                <el-form-item label="楼号" :label-width="formLabelWidth">
                    <el-input v-model="temp.address3"></el-input>
                </el-form-item>
                <el-form-item label="详细地址" :label-width="formLabelWidth">
                    <el-input v-model="temp.address4" ></el-input>
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
    import {getList, modifyinfo,modifydetail} from '@/api/order'
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
            appointtimeFilter(status) {
                const statusMap = {
                    5: '18:00-20:00',
                    4: '16:00-18:00',
                    3: '14:00-16:00',
                    2: '12:00-14:00',
                    1: '10:00-12:00',
                }
                return statusMap[status]
            },
            statusFilter(status) {
                const statusMap = {
                    5: 'danger',
                    4: 'success',
                    3: 'warning',
                    2: 'warning',
                    1: '',
                    0: 'info'
                }
                return statusMap[status]
            },
            statusReserve(status){
                const statusMap = {
                    5: '投诉中',
                    4: '已完成',
                    3: '待送货',
                    2: '待取货',
                    1: '预约中',
                    0: '已关闭'
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
                modifydetail(this.temp.orderid,temp, adminname).then(response => {
                    setTimeout(function () {
                        that.buttonLoading = false
                        that.dialogFormVisible = false

                    },300)
                    for (const v of this.list) {
                        if (v.orderid === this.temp.orderid) {
                            const index = this.list.indexOf(v)
                            this.list.splice(index, 1, this.temp)
                            break
                        }
                    }
                })
            },
            modifyInfo(id, type) {
                if (!this.checkroles(1)) {
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