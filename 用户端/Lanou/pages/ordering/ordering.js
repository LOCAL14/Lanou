// pages/me_wallet_coupon/me_wallet_coupon.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
    hasOrderData: false,
    hasOrdering: false,
    orderData: '',
    address: '',
    contactInfo: '',
    date: '',
    time: '',
    evaluateWeight: '',
    collectorName: '',
    collectorPhonenumber: '',
    remarks: '',
    url1: '',
    url2: '',
    url3: '',

  },
  navigatorToHistoryorder: function () {
    wx.navigateTo({
      url: '../historyorder/historyorder',
    })
  },

  callCollector: function () {
    wx.makePhoneCall({
      phoneNumber: this.data.collectorPhonenumber,
      success: function (res) { },
      fail: function (res) { },
    })
  },

  cancelOrder: function () {
    var that = this
    var session_now = wx.getStorageSync('user_session')
    var timestamp = new Date().getTime()
    timestamp = timestamp / 1000
    console.log(timestamp)
    if (this.data.orderData[12] + 125 > timestamp || !this.data.time) {
      wx.showModal({
        title: '确认取消订单',
        content: '下单两分钟内可以免费取消',
        success: function (res) {
          if (res.confirm) {
            wx.request({
              url: 'https://www.iamxz.net/controller/order.php?action=cancelorder',
              method: 'POST',
              header: {
                'content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
              },
              data: {
                session: session_now,
                orderid: that.data.orderData[11]
              },
              success: result => {
                if (result.data.result == 'success') {
                  wx.navigateTo({
                    url: '../historyorder/historyorder',
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
          }
        }
      })
    } else {
      wx.showModal({
        title: '下单已超过两分钟',
        content: '抱歉，请联系回收员撤销订单',
        confirmText: '立即联系',
        success: function (res) {
          if (res.confirm) {
            wx.makePhoneCall({
              phoneNumber: that.data.collectorPhonenumber,
              success: function (res) { },
              fail: function (res) { },
            })
          }
        }
      })
    }



  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this
    var session_now = wx.getStorageSync('user_session')

    wx.request({
      url: 'https://www.iamxz.net/controller/order.php?action=getordering',
      method: 'POST',
      header: {
        'content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      data: {
        session: session_now
      },
      success: result => {
        if (result.data.result == 'success') {
          fetch = result.data.msg
          console.log(fetch)
          switch (fetch[3]) {
            case '0': fetch[3] = '30分钟上门'
              break;
            case '1': fetch[3] = '10:00-12:00'
              break;
            case '2': fetch[3] = '12:00-14:00'
              break;
            case '3': fetch[3] = '14:00-16:00'
              break;
            case '4': fetch[3] = '16:00-18:00'
              break;
            case '5': fetch[3] = '18:00-20:00'
              break;
            default: '等待上门'
          }

          that.setData({
            orderData: fetch,
            hasOrdering: true,
            hasOrderData: true,
            address: fetch[0] + ' ' + fetch[13] + fetch[14],
            contactInfo: fetch[1],
            date: fetch[2],
            time: fetch[3],
            evaluateWeight: fetch[4],
            collectorName: fetch[5],
            collectorPhonenumber: fetch[6],
            remarks: fetch[7],
            url1: fetch[8],
            url2: fetch[9],
            url3: fetch[10],
          })
        } else if (result.data.msg == 'session已过期') {
          wx.showToast({
            title: '请重启小程序',
            image: '/image/wrong.png',
            duration: 3000
          })
        } else if (result.data.msg == '无进行中订单') {
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