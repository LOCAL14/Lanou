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
    input: false,
    phonenumber:'',
  },

  //获取输入的电话号码
  phonenumberSubmit: function (a) {
    phonenumber = parseInt(a.detail.value)
  },

  //点击按钮发送验证码
  getcode: function () {
    var that = this
    var session_now = wx.getStorageSync('user_session')

    if (phonenumber > 1e10 && phonenumber < 1e11) {
      wx.request({
        url: 'https://www.iamxz.net/controller/user.php?action=sendcode_changephone',
        method: 'POST',
        header: {
          'content-Type': 'application/x-www-form-urlencoded',
          // 'Accept': 'application/json',
          'Accept': 'application/x-www-form-urlencoded'
        },
        'data': {
          session: session_now,
          phonenumber: phonenumber
        },
        success: function (result) {
          if (result.data.result == 'success') {
            var countdown = 120
            var temp = setInterval(function () {
              if (countdown != 0) {
                that.setData({
                  button: countdown,
                  input: true
                })
                countdown--
              };
              if (countdown == 0) {
                that.setData({
                  button: '发送验证码',
                  input: false
                })
                countdown = 120
                clearInterval(temp)
              }
            }
              , 1050)      //1050是防止网速过慢
          } else {
            console.log(result.data.msg)
            wx.showToast({
              title: result.data.msg,
              image: '/image/wrong.png',
              duration: 2000
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
    } else {
      wx.showToast({
        title: '手机号格式有误',
        image: '/image/phone.png',
        duration: 2000
      })

    }
  },

  //获取输入的验证码
  codeSubmit: function (a) {
    code = parseInt(a.detail.value)
  },

  /**
   * 修改手机号函数 绑定于“下一步“按钮
   */

  changePhone: function () {
    var that = this
    var session_now = wx.getStorageSync('user_session')


    if (code && phonenumber) {
      wx.request({
        url: 'https://www.iamxz.net/controller/user.php?action=changephone',
        method: 'POST',
        header: {
          'content-Type': 'application/x-www-form-urlencoded',
          'Accept': 'application/json',
        },
        data: {
          session: session_now,
          phonenumber: phonenumber,
          code: code,
        },
        success: function (result) {
          if (result.data.result == 'success') {
            wx.showToast({
              title: '成功',
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

    } else if (phonenumber && !code) {
      wx.showToast({
        title: '请输入验证码',
        image: '/image/verifycode.png',
        duration: 3000
      })
    } else {
      wx.showToast({
        title: '手机号格式有误',
        image: '/image/phone.png',
        duration: 3000
      })
    }
  },


  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this
    var session_now = wx.getStorageSync('user_session')

    //请求手机号（隐藏）
    wx.request({
      url: 'https://www.iamxz.net/controller/user.php?action=getphonenumber',
      method: 'POST',
      header: {
        'content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json',
      },
      data: {
        session: session_now,
      },
      success: function (result) {
        if (result.data.result == 'success') {
          that.setData({
            phonenumber:result.data.msg
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





