var rawArray


{
  Page({
    /**
     * 页面的初始数据
     */
    data: {
      r:'', //rawArray
      array: '',
      hasAddress: false,
      selectIndex: 0
    },
    navigatorToAddNew: function () {
      wx.navigateTo({
        url: '../add_new_address/add_new_address',
      })
    },
    navigatorToModify:function(e){
      var i = e.currentTarget.dataset.id
      wx.navigateTo({
        url: '../modifyaddress/modifyaddress?oldAddress=' + rawArray[i] +'&addressIndex='+ i
      })
    },
    selectAddress: function (e) {
      var pages = getCurrentPages();
      var prevPage = pages[pages.length - 2];
      this.setData({
        selectIndex: e.currentTarget.dataset.id
      })
      prevPage.setData({
        selectIndex: e.currentTarget.dataset.id
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


      wx.request({
        url: 'https://www.iamxz.net/controller/user.php?action=getaddress',
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
            console.log(fetch)
            rawArray = fetch

            var temp = new Array()
            var i
            for (i = 0; i < fetch.length; i++) {
              var set = new Object();//temp为一维数组 set是对象
              set.address = fetch[i][4] + fetch[i][6] + fetch[i][8]
              set.contact = fetch[i][9] + ' ' + fetch[i][10]
              set.index = i
              temp[i] = set
            }
            console.log(temp)
            that.setData({
              r: fetch,
              array: temp,
              hasAddress: true,
              selectIndex: options.selectIndex
            })
          } else if (result.data.msg == 'session已过期') {
            var temp = wxlogin.wxLoginAgain()
            wx.showToast({
              title: '请重试',
              image: '/image/wrong.png',
              duration: 3000
            })
          } else if (result.data.msg == '无地址信息') {
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
      var app = getApp()
      var that = this
      var session_now = wx.getStorageSync('user_session')
      var fetch
      var temp


      wx.request({
        url: 'https://www.iamxz.net/controller/user.php?action=getaddress',
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
            console.log(fetch)
            rawArray = fetch

            var temp = new Array()
            var i
            for (i = 0; i < fetch.length; i++) {
              var set = new Object();//temp为一维数组 set是对象
              set.address = fetch[i][4] + fetch[i][6] + fetch[i][8]
              set.contact = fetch[i][9] + ' ' + fetch[i][10]
              set.index = i
              temp[i] = set
            }
            console.log(temp)
            that.setData({
              r: fetch,
              array: temp,
              hasAddress: true
            })
          } else if (result.data.msg == 'session已过期') {
            var temp = wxlogin.wxLoginAgain()
            wx.showToast({
              title: '请重试',
              image: '/image/wrong.png',
              duration: 3000
            })
          } else if (result.data.msg == '无地址信息') {
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