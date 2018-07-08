<template>
    <div class="app-container">
        <el-select v-model="listquery.title" placeholder="请选择搜索条件" class="select">
            <el-option label="社区ID" value="neighborhoodid"></el-option>
            <el-option label="社区名" value="name"></el-option>
            <el-option label="城市" value="city"></el-option>
        </el-select>
        <el-input placeholder="请输入搜索内容" v-model="listquery.value" class="input" :disabled="listquery.title?false:true">
            <el-button slot="append" icon="el-icon-search" @click="search"></el-button>
        </el-input>
        <el-button icon="el-icon-refresh" @click="reset">重置</el-button>
        <div style="width:100%;display: block;height: 100%">
            <el-table :data="roles >= 1?list:''" :empty-text="roles >= 1?'暂无数据':'你无权访问此数据'"
                      v-loading.body="listLoading" element-loading-text="加载数据中" border fit
                      highlight-current-row style="width: 100%" class="table-fixed el-table--scrollable-x">
                <el-table-column type="expand">
                    <template slot-scope="scope">
                        <p v-if="scope.row.building_info" style="color:#99a9bf">楼号设置详情</p>
                        <el-table :data="scope.row.building_info" v-if="scope.row.building_info"
                                  class="detailtable">
                            <el-table-column align="center" label='楼号' width="95">
                                <template slot-scope="scope">
                                    {{scope.$index + 1}}
                                </template>
                            </el-table-column>
                            <el-table-column align="center" label='名称'>
                                <template slot-scope="scope">
                                    {{scope.row}}
                                </template>
                            </el-table-column>

                        </el-table>
                    </template>
                </el-table-column>
                <el-table-column align="center" label='ID' width="95">
                    <template slot-scope="scope">
                        {{scope.row.neighborhoodid}}
                    </template>
                </el-table-column>
                <el-table-column label="社区名" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.name}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="位置" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.district}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="加入时间" width="200" align="center">
                    <template slot-scope="scope">
                        <span>{{scope.row.add_time}}</span>
                    </template>
                </el-table-column>
                <el-table-column class-name="status-col" label="状态" width="110" align="center">
                    <template slot-scope="scope">
                        <el-tag :type="scope.row.status | statusFilter">{{scope.row.status | statusReserve}}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" label="操作" width="220" fixed="right">
                    <template slot-scope="scope">
                        <el-button @click="toModifydetail(scope.row)" type="text" size="small">管理</el-button>
                        <el-button @click="modifyInfo(scope.row.neighborhoodid,'changestatus')" type="text"
                                   size="small">
                            {{scope.row.status === '0'?'恢复':'停用'}}
                        </el-button>
                        <el-button @click="modifyInfo(scope.row.neighborhoodid,'delete')" type="text" size="small">删除
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

        <el-dialog title="管理" :visible.sync="dialogFormVisible">
            <el-form :model="temp" status-icon style="width:60%;">
                <p style="color:#99a9bf">基础设置</p>
                <el-form-item label="所在地区">
                    <el-cascader
                            :options="cityOptions"
                            placeholder="可搜索区名"
                            @change="handleCityChange"
                            filterable
                            v-model="temp.district"
                    ></el-cascader>
                </el-form-item>
                <el-form-item label="社区名称" :label-width="formLabelWidth">
                    <el-input v-model="temp.name"></el-input>
                </el-form-item>
                <p style="color:#99a9bf">楼号设置</p>
                <template v-for="(item, index) in temp.building_info">
                    <el-form-item :label="'楼号'+(index+1)" :label-width="formLabelWidth">
                        <el-input v-model="temp.building_info[index]">
                            <el-button slot="append" icon="el-icon-close" @click="minusBuilding()"></el-button>
                        </el-input>
                    </el-form-item>
                </template>


            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button icon="el-icon-plus" @click="addBuilding()">增加楼号</el-button>
                <el-button @click="cancel">取 消</el-button>
                <el-button type="primary" :loading="buttonLoading" @click="modifydetail()">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import {getList, modifyinfo, modifydetail} from '@/api/area'
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
                dialogFormVisible: false,
                formLabelWidth: '70px',
                buttonLoading: false,
                cityOptions: citydata,
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
                    for (var item of this.list) {
                        if (item.building_info) {
                            item.building_info = JSON.parse(item.building_info)
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
            toModifydetail(rowData) {
                if (!this.checkroles(1)) {
                    return false
                }
                this.temp = Object.assign({}, rowData)
                this.temp.district = this.temp.district.split(',')

                this.dialogFormVisible = true

            },
            modifydetail() {
                if (!this.checkroles(2)) {
                    return false
                }
                for (var item of this.temp.building_info) {
                    if (!item) {
                        this.$notify({
                            title: '错误',
                            message: '未正确设置楼号',
                            type: 'error',
                            duration: 2000
                        })
                        return false
                    }
                }
                const adminname = this.name
                this.buttonLoading = true
                var array = this.temp.district
                if (!this.temp.district[2]) {
                    //直辖市
                    this.temp.district = array[0] + ',' + array[1]
                } else {
                    this.temp.district = array[0] + ',' + array[1] + ',' + array[2]
                }
                var that = this
                var temp = JSON.stringify(this.temp)
                modifydetail(this.temp.neighborhoodid, temp, adminname).then(response => {
                    setTimeout(function () {
                        that.buttonLoading = false
                        that.dialogFormVisible = false

                    }, 300)
                    for (const v of this.list) {
                        if (v.neighborhoodid === this.temp.neighborhoodid) {
                            const index = this.list.indexOf(v)
                            this.list.splice(index, 1, this.temp)
                            break
                        }
                    }
                })
            },
            cancel() {

                this.dialogFormVisible = false
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
            handleCityChange(array) {
                this.temp.district = array

            },
            addBuilding() {
                this.temp.building_info.push('')
            },
            minusBuilding() {
                this.temp.building_info.pop()
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