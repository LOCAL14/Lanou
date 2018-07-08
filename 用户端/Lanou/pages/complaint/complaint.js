// pages/complaint/complaint.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
    orderid:'',
    complaint_text: '',
    complaint_time: 0,
  },
  evaluateOrder: function () {
    var that = this
    var session_now = wx.getStorageSync('user_session')
    if (!this.data.complaint_text){
      wx.showToast({
        title: '请填写投诉内容',
        image: '/image/wrong.png',
        duration: 2000
      })
      return false
    }
    wx.request({
      url: 'https://www.iamxz.net/controller/order.php?action=complaintorder',
      method: 'POST',
      header: {
        'content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      data: {
        session: session_now,
        orderid: this.data.orderid,
        rate_text: this.data.complaint_text,
      },
      success: result => {
        if (result.data.result == 'success') {
          that.setData({
            'orderData.rate_star': that.data.rateStar,
            'orderData.rate_text': that.data.rateText,
            'orderData.rate_time': 10000,

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

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      orderid: options.orderid,
      complaint_text: options.complaint_text,
      complaint_time: options.complaint_time
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