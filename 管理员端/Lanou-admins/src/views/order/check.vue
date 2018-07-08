<template>
    <div class="app-container">
        <h3 style="margin:20px 0;">快速确认送货</h3>
        <h4 v-if="roles == 1">抱歉，你的操作权限不足</h4>
        <template v-if="roles != 1">
            <el-input placeholder="请输入回收员ID" v-model="listquery.value" class="input">
                <el-button slot="append" icon="el-icon-search" @click="search" :loading="buttonLoading"></el-button>
            </el-input>
            <el-button icon="el-icon-refresh" @click="reset">重置</el-button>
            <h4 v-if="!listTotal" style="color:#99a9bf">抱歉，暂无该回收员名下的待送货订单</h4>
            <div style="width:80%;display: block;margin:20px 0;" v-if="list">
                <el-table :data="list" empty-text="暂无数据"
                          v-loading.body="listLoading" element-loading-text="加载数据中" border fit
                          highlight-current-row class="table-fixed el-table--scrollable-x">
                    <el-table-column type="expand">
                        <template slot-scope="scope">
                            <p style="color:#99a9bf">回收详情</p>
                            <el-table :data="scope.row.waste_detail" style="width: 100%" class="detailtable">
                                <el-table-column label='类别' width="95">
                                    <template slot-scope="scope">
                                        {{scope.row[0]}}
                                    </template>
                                </el-table-column>
                                <el-table-column label='下单单价' width="95">
                                    <template slot-scope="scope">
                                        {{scope.row[1]}}
                                    </template>
                                </el-table-column>
                                <el-table-column label='数量/重量' width="95">
                                    <template slot-scope="scope">
                                        {{scope.row[2]}}
                                    </template>
                                </el-table-column>
                                <el-table-column label='总价'>
                                    <template slot-scope="scope">
                                        {{scope.row[3]}}
                                    </template>
                                </el-table-column>
                            </el-table>
                        </template>
                    </el-table-column>
                    <el-table-column align="center" label='订单ID' width="95">
                        <template slot-scope="scope">
                            {{scope.row.orderid}}
                        </template>
                    </el-table-column>
                    <el-table-column label="城市" width="110" align="center">
                        <template slot-scope="scope">
                            <span>{{scope.row.address5}}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="联系人" width="110" align="center">
                        <template slot-scope="scope">
                            <span>{{scope.row.contact_name}}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="手机号" align="center">
                        <template slot-scope="scope">
                            <span>{{scope.row.phonenumber}}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="回收员" width="110" align="center">
                        <template slot-scope="scope">
                            <span>{{scope.row.collector_name}}</span>
                        </template>
                    </el-table-column>
                    <el-table-column align="center" label="操作" width="110">
                        <template slot-scope="scope">
                            <el-button @click="checkorder(scope.row.orderid)" round size="small">确认送货</el-button>
                        </template>
                    </el-table-column>

                </el-table>
            </div>
        </template>
    </div>
</template>

<script>
    import {getList,checkorder} from "../../api/order";
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
                    title: 'collectorid',
                    value: ''
                },
                listquerynow: {
                    page: '',
                    limit: '',
                    city: '',
                    title: 'collectorid',
                    value: ''
                },
                dialogFormVisible: false,
                formLabelWidth: '70px',
                buttonLoading: false
            }
        },
        filters: {},
        computed: {
            ...mapGetters([
                'name',
                'city',
                'roles'
            ]),
        },
        created() {
            this.setQuerycity()
        },
        methods: {
            setQuerycity() {
                if (this.city) {
                    this.listquery.city = this.city
                }
            },
            fetchData(query) {
                this.listLoading = true
                this.buttonLoading = true
                var listquery = JSON.stringify(query)

                getList(listquery).then(response => {
                    this.list = response.items
                    if (response.items) {
                        for (var item of this.list) {
                            if (item.waste_detail) {
                                item.waste_detail = JSON.parse(item.waste_detail)
                            }
                        } //将waste_detail(Object)转化为Array
                    }
                    this.listTotal = response.total
                    this.listLoading = false
                    this.buttonLoading = false
                    this.listquerynow.page = query.page
                    this.listquerynow.limit = query.limit
                    this.listquerynow.city = query.city
                    this.listquerynow.title = query.title
                    this.listquerynow.value = query.value
                    this.currentPage = 1
                })
            },
            search() {
                var regPos = /^[1-9]\d*$/; // 非负整数
                if (regPos.test(this.listquery.value)) {
                    this.listquery.page = 1
                    this.fetchData(this.listquery)
                } else if (this.listquery.value) {
                    this.$notify({
                        title: '错误',
                        message: '请输入正确的回收员ID',
                        type: 'error',
                        duration: 2000
                    })
                } else {
                    this.$notify({
                        title: '错误',
                        message: '请输入搜索内容',
                        type: 'error',
                        duration: 2000
                    })
                }
            },
            reset() {
                this.listTotal = 1,
                    this.listquery.city = this.city
                this.listquery.limit = 20
                this.listquery.title = 'collectorid'
                this.listquery.value = ''
                this.list = null
            },
            checkorder(id) {
                const adminname = this.name
                checkorder(id, adminname).then(response => {
                    this.$notify({
                        title: '成功',
                        message: '操作成功',
                        type: 'success',
                        duration: 2000
                    })
                    for (const v of this.list) {
                        if (v.orderid === id) {
                            const index = this.list.indexOf(v)
                            this.list.splice(index, 1)
                            break
                        }
                    }
                })

            },
        },


    }
</script>

<style scoped>
    .input {
        width: 50%;
    }

    /deep/ .detailtable th {
        border-right: 0 !important;
    }

    /deep/ .detailtable td {
        border-right: 0 !important;
    }

</style>

