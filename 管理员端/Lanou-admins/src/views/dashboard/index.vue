<template>
    <el-container class="container">
        <el-row class="row1">
            <el-col :span="6">
                <div class="status-block" style="background-color:#409EFF">
                    <i class="el-icon-plus"></i>
                    <div>
                        <p>新增用户数</p>
                        <p class="number">{{newuser}}</p>
                    </div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="status-block" style="background-color:#ffca14">
                    <i class="el-icon-service"></i>
                    <div>
                        <p>投诉订单数</p>
                        <p class="number">{{complaint}}</p>
                    </div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="status-block" style="background-color:#d25018">
                    <i class="el-icon-tickets"></i>
                    <div>
                        <p>低评价订单数</p>
                        <p class="number">{{lowrateorder}}</p>
                    </div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="status-block" style="background-color:#f6175e">
                    <i class="el-icon-close"></i>
                    <div>
                        <p>失败提现数</p>
                        <p class="number">{{failwithdraw}}</p>
                    </div>
                </div>
            </el-col>
        </el-row>
        <el-row class="row2" style="background:#fff;padding:26px 16px 0;margin-bottom:32px;">
            <line-chart :chart-data="lineChartData"></line-chart>
        </el-row>
    </el-container>
</template>

<script>
    import {mapGetters} from 'vuex'
    import LineChart from './LineChart'
    import {getData} from '@/api/dashboard'

    export default {
        name: 'dashboard',
        components: {
            LineChart
        },
        data() {
            return {
                newuser: 0,
                complaint: 0,
                lowrateorder: 0,
                failwithdraw: 0,
                lineChartData: {
                    aData: [100, 120, 161, 134, 105, 160, 165],
                    bData: [120, 82, 91, 154, 162, 140, 145]
                },
            }
        },
        computed: {
            ...mapGetters([
                'name',
                'roles'
            ])
        },
        created() {
            this.setData()

        },
        methods: {
            setData(){
                getData().then(response => {
                    this.newuser = response[0]
                    this.complaint = response[1]
                    this.lowrateorder = response[2]
                    this.failwithdraw = response[3]
                    this.lineChartData.aData = response[4][0]
                    this.lineChartData.bData = response[4][1]


                })
            }
        }
    }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
    .container {
        flex-direction: column;
    }

    .row1 {
        height: 120px;
        width: 100%;
        padding: 10px 10px 0 10px;
        .el-col {
            height: 120px;
            padding: 10px;
            .status-block {
                width: 100%;
                background-color: #2DCB70;
                height: 100px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                i {
                    color: #fff;
                    font-size: 60px;
                    font-weight: bold;
                    padding-left: 20px;
                }
                div {
                    color: #fff;
                    padding-right: 20px;
                    p {
                        margin: 0;
                        text-align: center;
                    }
                    .number {
                        font-size: 36px;
                        font-weight: bold;
                        margin: 5px 0 5px 0;
                    }
                }
            }
        }
    }

    .row2 {
        height: 400px;

    }
</style>
