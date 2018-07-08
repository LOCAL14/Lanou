{
  Page({

    /**
     * 页面的初始数据
     */
    data: {
      hasOrderData: false,
      orderData: '',
      rateStar: 0,
      rateText: '',
    },
    changeRateStar: function (e) {
      var i = e.currentTarget.dataset.id
      this.setData({
        rateStar: i
      })
    },
    changeRateText: function (e) {
      var i = e.detail.value
      this.setData({
        rateText: i
      })
    },
    navigatorToDetail: function () {
      var items = this.data.orderData.waste_detail
      var name = this.data.orderData.collector_name
      wx.navigateTo({
        url: '../order_detail/order_detail?items=' + items + '&name=' + name
      })
    },
    navigatorToComplaint: function () {
      var id = this.data.orderData.orderid
      var time = this.data.orderData.complaint_time
      var text = this.data.orderData.complaint_text
      wx.navigateTo({
        url: '../complaint/complaint?complaint_time=' + time + '&complaint_text=' + text + '&orderid=' + id
      })
    },
    evaluateOrder: function () {
      var that = this
      var session_now = wx.getStorageSync('user_session')

      wx.request({
        url: 'https://www.iamxz.net/controller/order.php?action=evaluateorder',
        method: 'POST',
        header: {
          'content-Type': 'application/x-www-form-urlencoded',
          'Accept': 'application/json'
        },
        data: {
          session: session_now,
          orderid: this.data.orderData.orderid,
          rate_star: this.data.rateStar,
          rate_text: this.data.rateText,
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
      var that = this
      var session_now = wx.getStorageSync('user_session')

      wx.request({
        url: 'https://www.iamxz.net/controller/order.php?action=getorderinfo',
        method: 'POST',
        header: {
          'content-Type': 'application/x-www-form-urlencoded',
          'Accept': 'application/json'
        },
        data: {
          session: session_now,
          orderid: options.orderid
        },
        success: result => {
          if (result.data.result == 'success') {
            fetch = result.data.msg
            if (fetch.appoint_date == 0) {
              fetch.appoint_date = fetch.order_time
            }
            if (fetch.appoint_time != 0) {
              switch (fetch.appoint_time) {
                case "1": fetch.appoint_time = "10:00-12:00";
                  break;
                case "2": fetch.appoint_time = "12:00-14:00";
                  break;
                case "3": fetch.appoint_time = "14:00-16:00";
                  break;
                case "4": fetch.appoint_time = "16:00-18:00";
                  break;
                case "5": fetch.appoint_time = "18:00-20:00";
                  break;
                default: fetch.appoint_time = "预约单";
              }
            } else {
              fetch.appoint_time = "30分钟内"
            }
            console.log(fetch)
            that.setData({
              orderData: fetch,
              hasOrderData: true,
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
}


