<template>
    <div class="app-container">
        <h4 v-if="roles < 2">抱歉，你的操作权限不足</h4>
        <el-row v-if="roles > 1">
            <el-col :span="16">
                <el-button icon="el-icon-refresh" @click="fetchData">刷新</el-button>
                <template v-for="(item,index) of list">
                    <h3 style="color:#99a9bf">特推商品位-{{item.id}}</h3>
                    <el-card class="card" :body-style="{ padding: '0px',height:'100%', }">
                        <img :src="item.url" class="card-image">
                        <div class="card-right">
                            <span>
                                <p class="title">{{item.name}}</p>
                                <p class="introduction">{{item.unitprice}}</p>
                            </span>
                            <span class="button">
                                <el-button size="mini" type="info" round @click="toModifydetail(item)">修改信息</el-button>
                            </span>
                        </div>
                    </el-card>
                </template>
            </el-col>
            <el-col :span="8">
                <div class="preview-text">
                    <h3>特推商品</h3>
                    <p>特推商品展示在首页的商品位上，用于展示特价商品</p>
                    <p class="title">&nbsp;</p>
                </div>
                <div class="preview">
                    <img class="img" src="https://ws4.sinaimg.cn/large/006tKfTcly1forn0fg6jxj30kw0g2di5.jpg">
                    <div class="cards">
                        <template v-for="item of list">
                            <div class="card">
                                <img :src="item.url">
                                <p>{{item.unitprice}}</p>
                            </div>
                        </template>
                    </div>
                    <img class="img" src="https://ws3.sinaimg.cn/large/006tKfTcly1forn0qqx88j30kw0e8jrw.jpg">
                </div>
            </el-col>
        </el-row>

        <el-dialog title="修改商品" :visible.sync="modifyFormVisible">
            <el-form :model="temp" status-icon style="width:60%;">
                <el-form-item label="商品名称" label-width="70px">
                    <el-input v-model="temp.name"></el-input>
                </el-form-item>
                <el-form-item label="单价" label-width="70px">
                    <el-input v-model="temp.unitprice"
                              type="textarea" :autosize="{ minRows: 1, maxRows: 2}"></el-input>
                </el-form-item>
                <el-form-item label="图片地址" label-width="70px">
                    <el-input v-model="temp.url"></el-input>
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
                        <el-button size="small" type="primary">上传轮播图</el-button>
                        <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb。<br>图片尺寸为116px*92px。
                        <br>重新上传会覆盖原来的图片地址</div>
                    </el-upload>
                </el-form-item>

            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="modifyFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="modifydetail">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>

<script>
    import {getList_goods, modifydetail_goods} from '@/api/activity'
    import {mapGetters} from 'vuex'

    export default {
        data() {
            return {
                list: null,
                temp: {
                    url : '',
                },
                filename: '',
                uploadData: {
                    'key': '',
                    'success_action_status': '200'
                },
                modifyFormVisible: false,
            }
        },
        computed: {
            ...mapGetters([
                'name',
                'city',
                'roles'
            ]),
        },
        filters: {},
        created() {
            this.fetchData()
            this.renameFile()
        },
        methods: {
            fetchData() {
                getList_goods().then(response => {
                    this.list = response
                })

            },
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
                this.uploadData.key = 'goods/' + this.filename
                //命名规则：毫秒级时间戳+五位随机字母(无字母O)
            }, //上传OSS前重命名图片
            getUrl(res, file, fileList) {
                this.temp.url = 'https://iamxz-net.oss-cn-hangzhou.aliyuncs.com//goods/' + this.filename
            },

            toModifydetail(rowData) {
                this.temp = Object.assign({}, rowData)
                this.modifyFormVisible = true

            },
            modifydetail() {
                const adminname = this.name
                this.temp.id = parseInt(this.temp.id)
                var that = this
                var temp = JSON.stringify(this.temp)

                modifydetail_goods(this.temp.id, temp, adminname).then(response => {
                    setTimeout(function () {
                            that.modifyFormVisible = false
                        }
                        , 200)
                    this.fetchData()
                })

            },
        }


    }
</script>

<style rel="stylesheet/scss" lang="scss">
    .card {
        height: 180px;
        width: 100%;
        margin: 10px 0;
        .el-card__body {
            display: flex;
            position: relative;
        }
        .card_switch {
            position: absolute;
            top: 10px;
            right: 10px;

        }
        .card_switch:before {
            content: '状态';
            margin-right: 5px;
            color: #99a9bf;
            opacity: 0;
            transition: opacity 0.6s ease-in-out;

        }
        .card_switch:hover::before {
            opacity: 1;

        }
        .card-image {
            min-width: 260px;
            max-width: 260px;
            height: 100%;

        }
        .card-right {
            height: 100%;
            display: flex;
            flex-grow: 1;
            flex-direction: column;
            justify-content: space-between;
            padding: 10px;
            p {
                margin: 0 0 10px 0;
            }
            .title {
                font-size: 18px;
                font-weight: bold;
                color: #2DCB70;
                margin-bottom: 20px;
            }
            .introduction {
                color: #99a9bf;
                width: 70%;
                overflow-y: auto;

            }
            .url {
                color: #99a9bf;
                width: 70%;
                display: block;
                text-overflow: ellipsis;
                overflow: hidden;
                text-decoration-line: underline;
            }
            .button {
                display: inline-flex;
                align-self: flex-end;
                padding-left: 5px;

            }

        }
    }

    .preview-text {
        width: 70%;
        margin: 50px auto 0 auto;

        h3 {
            margin: 0;
        }
        p {
            margin: 5px 0 10px 0;
            font-size: 16px;
            color: #99a9bf;
        }
        .title {
            color: #99a9bf;
            font-size: 12px;
            margin:0;
        }
    }

    .preview {
        width: 70%;
        margin: 0 auto;
        box-shadow: 0 2px 12px 0 rgba(0,0,0,.1);
        border: 1px solid #ebeef5;
        border-radius: 4px;
        .cards{
            width:100%;
            height:100px;
            display: flex;
            justify-content: space-between;

            .card{
                width:32%;
                height:100px;
                margin:0px;
                box-shadow: 0 2px 12px 0 rgba(0,0,0,.1);
                border: 1px solid #ebeef5;
                border-radius: 4px;
                img{
                    width:100%;
                    min-height:80px;
                    max-height:80px;
                }
                p{
                    margin:0px;
                    height: 20px;
                    text-align: center;
                    font-size: 10px;
                }
            }
        }

        .img {
            width: 100%;
            filter: brightness(0.3);
        }
    }


</style>