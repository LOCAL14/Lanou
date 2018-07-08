// pages/me_wallet_withdraw/me_wallet_withdraw.js
const app = getApp()
var withdrawValue


Page({

  /**
   * 页面的初始数据
   */
  data: {
    value: '',
    userInfo:'',
    wxuser_avatarUrl: '',
    wxuser_nickName: '',
    hasUserInfo: false,
    value : '',
  },
  
  //获取输入内容，并检查金额不超过余额
  input: function (event) {
    if (event.detail.value > parseFloat(app.globalData.balance) ){
      this.setData({
        value: parseFloat(app.globalData.balance) 
      })
      withdrawValue = parseFloat(app.globalData.balance) 
     }else if (event.detail.value < 1e-2){
      this.setData({
        value: 0.01
      })
      withdrawValue = 0.01
      wx.showToast({
        title: '最低提现0.01',
        image: '/image/wrong.png',
        duration:1000
      })
     }else {
      withdrawValue = event.detail.value
    }
  },

  //全部提现功能
  putValue :function(){
    this.setData({
      value: parseFloat(app.globalData.balance)
    })
    withdrawValue = parseFloat(app.globalData.balance) 
  },

  withdraw:function(){

    var that = this
    var session_now = wx.getStorageSync('user_session')
    
    //发起提现请求  
    wx.request({
      url: 'https://www.iamxz.net/controller/withdraw.php?action=new',
      method: 'POST',
      header: {
        'content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      data: {
        session: session_now,
        withdrawvalue: withdrawValue
      },
      success: result => {
        if (result.data.result == 'success') {
          wx.navigateTo({
            url: '../me_wallet_withdraw_success/me_wallet_withdraw_success',
          })
        } else if (result.data.msg == 'session已过期'){
          wx.showToast({
            title: '请重启小程序',
            image: '/image/wrong.png',
            duration: 3000
          })
        }else{
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

    //获取用户信息，显示在提现账户中
    if (app.globalData.userInfo) {
      this.setData({
        userInfo: app.globalData.userInfo,
        wxuser_avatarUrl : app.globalData.userInfo.avatarUrl,
        wxuser_nickName : app.globalData.userInfo.nickName,
        hasUserInfo: true
      })
    } else if (this.data.canIUse) {
      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
      // 所以此处加入 callback 以防止这种情况
      app.userInfoReadyCallback = res => {
        this.setData({
          userInfo: res.userInfo,
          wxuser_avatarUrl : res.userInfo.avatarUrl,
          wxuser_nickName : res.userInfo.nickName,
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
            wxuser_avatarUrl : res.userInfo.avatarUrl,
            wxuser_nickName : res.userInfo.nickName,
            hasUserInfo: true
          })
        }
      })
    }

    

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