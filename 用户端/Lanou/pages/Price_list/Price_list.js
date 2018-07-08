
var point = 1;
var sign = 1;
var sign_last = 1;
var height;

wx.getSystemInfo({
  success: function(res) {
    height = res.windowHeight*0.96
    console.log(height)
  },
})

Page({
  
  data: {
    list:[],
    activeIndex : 0,
    scrollTop: 0,
    height: height
  },
  
  changelist:function(e){
    let id = e.target.dataset.id
    this.setData({
      activeIndex: id,
    })
  },

  onLoad: function (options) {
    var app = getApp()
    var that = this
    var session_now = wx.getStorageSync('user_session')


    wx.request({
      url: 'https://www.iamxz.net/view/pricelist.php?action=fetchpricelist',
      method: 'POST',
      header: {
        'content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      success: result => {
        if (result.data.result == 'success') {
          var temp = result.data.msg
          for(var item of temp){
            item.goods = JSON.parse(item.goods)
          }
          that.setData({
            list: temp,

          })
        } else if (result.data.msg == 'session已过期') {
          wx.showToast({
            title: '请重启小程序',
            image: '/image/wrong.png',
            duration: 3000
          })
        } else {
          wx.showToast({
            title: result.data.msg,
            image: '/image/wrong.png',
            duration: 3000
          })
        }
      },
      fail: res => {
        wx.showToast({
          title: '网络不好哟',
          image: '/image/wrong.png',
          duration: 3000
        })
      }
    })
  },



})
