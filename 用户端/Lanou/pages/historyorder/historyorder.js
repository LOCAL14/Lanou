var wxlogin = require('../../JS/wxlogin.js')
{
  Page({
    /**
     * 页面的初始数据
     */
    data: {
      array: '',
      hasHistoryOrder: false
    },
    navigatorToOrdering: function () {
      wx.navigateTo({
        url: '../ordering/ordering',
      })
    },
    navigatorToDetail: function (e) {
      var i = e.currentTarget.dataset.id
      wx.navigateTo({
        url: '../evaluate/evaluate?orderid=' + i
      })
    },
    

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function (options) {
      var app = getApp()
      var that = this
      var session_now = wx.getStorageSync('user_session')
      var fetch
      var temp

      //历史订单fetch 
      wx.request({
        url: 'https://www.iamxz.net/controller/order.php?action=fetch_history',
        method: 'POST',
        header: {
          'content-Type': 'application/x-www-form-urlencoded',
          'Accept': 'application/json'
        },
        data: {
          session: session_now,
        },
        success: result => {
          if (result.data.result == 'success') {
            fetch = result.data.msg
            for (var item of fetch) {
              item.address = item.address5 + item.address2 + item.address3
            }
            console.log(fetch)
            that.setData({
              array: fetch,
              hasHistoryOrder: true
            })
          } else if (result.data.msg == 'session已过期') {
            var temp = wxlogin.wxLoginAgain()
            wx.showToast({
              title: '请重试',
              image: '/image/wrong.png',
              duration: 3000
            })

          } else if (result.data.msg == '无历史订单') {
            //do nothing
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

    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady: function () {

    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function () {

    },

    /**
     * 生命周期函数--监听页面隐藏
     */
    onHide: function () {

    },

    /**
     * 生命周期函数--监听页面卸载
     */
    onUnload: function () {

    },

    /**
     * 页面相关事件处理函数--监听用户下拉动作
     */
    onPullDownRefresh: function () {

    },

    /**
     * 页面上拉触底事件的处理函数
     */
    onReachBottom: function () {

    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function () {

    }
  })
}