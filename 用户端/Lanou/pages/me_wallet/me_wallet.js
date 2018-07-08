// pages/me_wallet/me_wallet.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
    balance: '0.00',


  },
  navigator: function () {
    wx.navigateTo({
      url: '../me_wallet_withdraw/me_wallet_withdraw',

    })
  },
  navigator2: function () {
    wx.navigateTo({
      url: '../me_wallet_coupon/me_wallet_coupon',

    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var app = getApp()
    var that = this
    var session_now = wx.getStorageSync('user_session')


    wx.request({
      url: 'https://www.iamxz.net/controller/user.php?action=getusermoney',
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
          that.setData({
            balance: result.data.msg,

          })
          app.globalData.balance = result.data.msg

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