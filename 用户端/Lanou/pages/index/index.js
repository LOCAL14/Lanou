//index.js
//获取应用实例
const app = getApp()
var that = this

Page({
  data: {
    url1:'',
    url2: '',
    url3: '',
    unitprice1: '',
    unitprice2: '',
    unitprice3: '',
    array: '',
  },
  //事件处理函数
  bindViewTap: function() {
    wx.navigateTo({
      url: '../logs/logs'
    })
  },
  // bindnavigator: function(){
  //   wx.navigateTo({
  //     url: '../signup/signup'
  //   })
  // },

    bindredirectto: function(){
      // 商品列表fetch

      var that = this
      var fetch
      var temp
      wx.request({
        url: 'https://www.iamxz.net/controller/homepage_fetch.php',
        method: 'POST',
        success: function (result) {
          fetch = result.data.msg
          temp = fetch[0].name
          that.setData({
            url1: fetch[0].url,
            url2: fetch[1].url,
            url3: fetch[2].url,
            unitprice1: fetch[0].unitprice,
            unitprice2: fetch[1].unitprice,
            unitprice3: fetch[2].unitprice,
          })
        }
      })
    },
bindnavigator: function(){
var that = this
var fetch
var temp
//data array : '' 
wx.request({
      url: 'https://www.iamxz.net/controller/homepage_fetch.php',
      method: 'POST',
      success: function (result) {
        fetch = result.data.msg
        console.log(fetch)
        temp = fetch[0].name
        console.log(temp)
        that.setData({
          array: fetch
        })
      }
    })
 }  , 
    



  
  
  onLoad: function () {
    if (app.globalData.userInfo) {
      this.setData({
        userInfo: app.globalData.userInfo,
        hasUserInfo: true
      })
    } else if (this.data.canIUse){
      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
      // 所以此处加入 callback 以防止这种情况
      app.userInfoReadyCallback = res => {
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
      }
    } else {
      // 在没有 open-type=getUserInfo 版本的兼容处理
      wx.getUserInfo({
        success: res => {
          app.globalData.userInfo = res.userInfo
          this.setData({
            userInfo: res.userInfo,
            hasUserInfo: true
          })
        }
      })
    }
  },
  getUserInfo: function(e) {
    console.log(e)
    app.globalData.userInfo = e.detail.userInfo
    this.setData({
      userInfo: e.detail.userInfo,
      hasUserInfo: true
    })
  }
})
