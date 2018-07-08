<template>
    <div class="app-container">
        <el-select v-model="listquery.title" placeholder="请选择搜索条件" class="select">
            <el-option label="管理员ID" value="id"></el-option>
            <el-option label="用户名" value="username"></el-option>
            <el-option label="城市" value="city"></el-option>
        </el-select>
        <el-input placeholder="请输入搜索内容" v-model="listquery.value" class="input" :disabled="listquery.title?false:true">
            <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
        </el-input>
        <el-button icon="el-icon-refresh" @click="reset">重置</el-button>
        <div style="width:100%;display: block;height: 100%">
            <el-table :data="roles > 1?list:''" :empty-text="roles > 1?'暂无数据':'你无权访问此数据'"
                      v-loading.body="listLoading" element-loading-text="加载数据中" border fit
                      highlight-current-row style="width: 100%" class="table-fixed el-table--scrollable-x">
                <el-table-column align="center" label='ID' width="95">
                    <template slot-scope="scope">
                        {{scope.row.id}}
                    </template>
                </el-table-column>
                <el-table-column label="用户名" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.username}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="分管城市" width="200" align="center">
                    <template slot-scope="scope">
                        <span v-if="scope.row.city">{{scope.row.city}}</span>
                        <span v-if="!scope.row.city">—</span>
                    </template>
                </el-table-column>
                <el-table-column class-name="status-col" label="权限" width="110" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.role | rolesFilter}}</span>
                    </template>
                </el-table-column>
                <el-table-column class-name="status-col" label="状态" width="110" align="center">
                    <template slot-scope="scope">
                        <el-tag :type="scope.row.status | statusFilter">{{scope.row.status | statusReserve}}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" label="操作" width="220" fixed="right">
                    <template slot-scope="scope">
                        <el-button @click="modifyInfo(scope.row.id,'changestatus')" type="text"
                                   size="small" :disabled="scope.row.username == name?true:false">
                            {{scope.row.status === '0'?'恢复':'停用'}}
                        </el-button>
                        <el-button @click="modifyInfo(scope.row.id,'delete')" type="text" size="small"
                                   :disabled="scope.row.username == name?true:false">删除
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
    </div>
</template>

<script>
    import {getList, modifyinfo, modifydetail} from '@/api/adminuser'
    import citydata from "../../components/citydata/citydata";
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
                temp: {
                    building_info: '',
                },
                formLabelWidth: '70px',
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
                    1: 'success',
                    0: 'danger'
                }
                return statusMap[status]
            },
            statusReserve(status) {
                const statusMap = {
                    1: '正常',
                    0: '停用'
                }
                return statusMap[status]
            },
            rolesFilter(status) {
                const statusMap = {
                    3: '中心运营',
                    2: '城市运营',
                    1: '客服',
                    0: '回收站员工'
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
            modifyInfo(id, type) {
                if (!this.checkroles(2)) {
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

            },
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
        .detailtable {
            width: 500px;
        }
        /deep/ .detailtable th {
            border-right: 0 !important;
        }
        /deep/ .detailtable td {
            border-right: 0 !important;
        }
    }
</style>