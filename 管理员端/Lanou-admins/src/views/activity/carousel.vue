<template>
    <div class="app-container">
        <h4 v-if="roles < 2">抱歉，你的操作权限不足</h4>
        <el-row v-if="roles > 1">
            <el-col :span="16">
                <el-button icon="el-icon-plus" @click="addFormVisible = true">添加轮播活动</el-button>
                <el-button icon="el-icon-refresh" @click="fetchData"></el-button>
                <h3 v-if="!list" style=" color: #99a9bf;">暂时没有轮播活动</h3>
                <template v-for="(item,index) of list">
                    <el-card class="card" :body-style="{ padding: '0px',height:'100%', }">
                        <el-switch class="card_switch" active-color="#13ce66" inactive-color="#99a9bf"
                                   v-model="item.status" @change="modifyInfo(index,'changestatus')">
                        </el-switch>
                        <img :src="item.url" class="card-image">
                        <div class="card-right">
                            <span>
                                <p class="title">{{item.name}}</p>
                                <p class="introduction">{{item.activity_introduction}}</p>
                                <a :href="item.activity_url" class="url">{{item.activity_url}}</a>
                            </span>
                            <span class="button">
                                <el-button size="mini" type="info" round @click="toModifydetail(item)">修改</el-button>
                                <el-button size="mini" type="danger" round
                                           @click="modifyInfo(index,'delete')">删除</el-button>
                            </span>
                        </div>
                    </el-card>
                </template>
            </el-col>
            <el-col :span="8">
                <div class="preview-text">
                    <h3>轮播活动</h3>
                    <p>轮播活动的展示位置在小程序首页的轮播图，点击会进入H5页面</p>
                    <p class="title">&nbsp;</p>
                </div>
                <div class="preview">
                    <img class="img" src="https://ws2.sinaimg.cn/large/006tNc79ly1foqz97cxjhj30ku03wq37.jpg">
                    <el-carousel height="160px" class="carousel">
                        <template v-for="item of list" >
                            <el-carousel-item  v-if="item.status == 1?true:false" :key="item[list]">
                                <img v-bind:src="item.url">
                            </el-carousel-item>
                        </template>
                    </el-carousel>
                    <img class="img" src="https://ws4.sinaimg.cn/large/006tNc79ly1foqz1ozyhnj30ku0leadn.jpg">
                </div>
            </el-col>
        </el-row>
        <el-dialog title="添加轮播活动" :visible.sync="addFormVisible">
            <el-form :model="form" status-icon style="width:60%;">
                <el-form-item label="活动名称" label-width="70px">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>
                <el-form-item label="轮播图" label-width="70px">
                    <el-upload
                            class="upload-demo"
                            action="https://iamxz-net.oss-cn-hangzhou.aliyuncs.com"
                            :data="uploadData"
                            list-type="picture"
                            :limit="1"
                            :before-upload="renameFile"
                            :on-success="getUrl">
                        <el-button size="small" type="primary">上传轮播图</el-button>
                        <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb。<br>图片尺寸为360px*210px。</div>
                    </el-upload>
                </el-form-item>
                <el-form-item label="活动简介" label-width="70px">
                    <el-input v-model="form.activity_introduction"
                              type="textarea" :autosize="{ minRows: 1, maxRows: 2}"></el-input>
                </el-form-item>
                <el-form-item label="H5地址" label-width="70px">
                    <el-input v-model="form.activity_url"></el-input>
                </el-form-item>

            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="canceladd">取 消</el-button>
                <el-button type="primary" @click="add">确 定</el-button>
            </div>
        </el-dialog>

        <el-dialog title="修改活动" :visible.sync="modifyFormVisible">
            <el-form :model="temp" status-icon style="width:60%;">
                <el-form-item label="活动名称" label-width="70px">
                    <el-input v-model="temp.name"></el-input>
                </el-form-item>
                <el-form-item label="轮播图" label-width="70px">
                    <el-input v-model="temp.url"></el-input>
                </el-form-item>
                <el-form-item label="活动简介" label-width="70px">
                    <el-input v-model="temp.activity_introduction"
                              type="textarea" :autosize="{ minRows: 1, maxRows: 2}"></el-input>
                </el-form-item>
                <el-form-item label="H5地址" label-width="70px">
                    <el-input v-model="temp.activity_url"></el-input>
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
    import {getList, modifyinfo, modifydetail, newActivity} from '@/api/activity'
    import {mapGetters} from 'vuex'

    export default {
        data() {
            return {
                list: null,
                form: {
                    name: '',
                    url: '',
                    activity_introduction: '',
                    activity_url: '',
                },
                temp: {},
                filename: '',
                uploadData: {
                    'key': '',
                    'success_action_status': '200'
                },
                addFormVisible: false,
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
                getList().then(response => {
                    for (var item of response) { //前端发过来的status是字符型的值
                        if (item.status == '1') {
                            item.status = true
                        } else {
                            item.status = false
                        }
                    }
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
                this.uploadData.key = 'carousel/' + this.filename
                //命名规则：毫秒级时间戳+五位随机字母(无字母O)
            }, //上传OSS前重命名图片
            getUrl(res, file, fileList) {
                this.form.url = 'https://iamxz-net.oss-cn-hangzhou.aliyuncs.com//carousel/' + this.filename
            },
            modifyInfo(id, type) {
                const adminname = this.name
                id += 1
                modifyinfo(id, type, adminname).then(response => {
                    this.$notify({
                        title: '成功',
                        message: '操作成功',
                        type: 'success',
                        duration: 2000
                    })
                    this.fetchData()
                })

            },
            add() {
                for (var item in this.form) {
                    if (!this.form[item]) {
                        this.$notify({
                            title: '错误',
                            message: '信息填写不完整',
                            type: 'error',
                            duration: 2000
                        })
                        return false
                    }
                }
                const adminname = this.name
                var temp = JSON.stringify(this.form)
                newActivity(temp, adminname).then(response => {
                    this.addFormVisible = false
                    this.fetchData()
                })
            },
            canceladd() {
                for (var item in this.form) {
                    this.form[item] = ''
                }
                this.addFormVisible = false
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

                modifydetail(this.temp.id, temp, adminname).then(response => {
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
        .carousel {
            .el-carousel__item {
                background-color: #99a9bf;
                img {
                    width: 100%;
                }
            }
        }
        .img {
            width: 100%;
            filter: brightness(0.3);
        }
    }


</style>