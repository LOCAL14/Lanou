// pages/signup/signup.js
const app = getApp()

var phonenumber
var code
var session_now

var wxuser_avatarUrl
var wxuser_nickName
var wxuser_code
var hasUserInfo = false

Page({
  
  /**
   * 页面的初始数据
   */
  data: {
    // height: 0,          //背景图片高度
    button: '发送验证码',   //按钮上的文字
    input: false
  },

  //获取输入的电话号码
  phonenumberSubmit: function (a) {
    phonenumber = parseInt(a.detail.value)
  },

  //回收员登录
  login:function(){
    wx.navigateTo({
      url: '../collector/collector',
    })
  },


  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

    wx.getStorage({
      key: 'user_session',
      success: res => {
        session_now = res.data
      }
    })             //变量赋值之后不能马上使用，要间隔一会儿再用

    

    /**
     * 微信用户信息获取
     * */
    if (app.globalData.userInfo) {
      wxuser_avatarUrl = app.globalData.userInfo.avatarUrl
      wxuser_nickName = app.globalData.userInfo.nickName
      hasUserInfo = true
    } else if (this.data.canIUse) {
      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
      // 所以此处加入 callback 以防止这种情况
      app.userInfoReadyCallback = res => {
        wxuser_avatarUrl = res.userInfo.avatarUrl
        wxuser_nickName = res.userInfo.nickName
        hasUserInfo = true
      }
    } else {
      // 在没有 open-type=getUserInfo 版本的兼容处理
      wx.getUserInfo({
        success: res => {
          app.globalData.userInfo = res.userInfo
          wxuser_avatarUrl = res.userInfo.avatarUrl
          wxuser_nickName = res.userInfo.nickName
          hasUserInfo = true
        }
      })
    }
  },
  getUserInfo: function (e) {
    console.log(e)
    app.globalData.userInfo = e.detail.userInfo
    this.setData({
      userInfo: e.detail.userInfo,
      hasUserInfo: true
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
//验证用户是否注册过
//var session_now
//wx.getStorage({
//   key: 'user-session',
//   success: function (res) {
//     session_now = res.data
//   }
// })
//
// wx.request({
//   url: 'https://www.iamxz.net/controller/checksignup.php',
//   method: 'POST',
//   header: {
//     'content-Type': 'application/x-www-form-urlencoded',
//     'Accept': 'application/json'
//   },
//   data: {
//     session:session_now
//   },
//   success: function (result) {
//     this.globalData.hasSignup = result.data.msg
//   }
// })