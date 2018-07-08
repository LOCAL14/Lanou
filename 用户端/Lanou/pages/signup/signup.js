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

  //点击按钮发送验证码
  getcode: function () {
    var that = this
    if (phonenumber > 1e10 && phonenumber < 1e11) {
      wx.request({
        url: 'https://www.iamxz.net/controller/user.php?action=sendcode',
        method: 'POST',
        header: {
          'content-Type': 'application/x-www-form-urlencoded',
          // 'Accept': 'application/json',
          'Accept': 'application/x-www-form-urlencoded'
        },
        'data': {
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
                  input:false
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
   * 注册函数 绑定于“下一步“按钮
   */

  signup: function () {
    
  
    if (code && phonenumber) {
      
      console.log(hasUserInfo)
      if (hasUserInfo) {
        
        wx.request({
          url: 'https://www.iamxz.net/controller/user.php?action=new',
          method: 'POST',
          header: {
            'content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json',
          },
          data: {
            session: session_now,
            phonenumber: phonenumber,
            thecode: code,
            wxnickname: wxuser_nickName,
            wxavatarurl: wxuser_avatarUrl,
            //wxOpenid于code2session.php获取
          },
          success: function (result) {
            if (result.data.result == 'success') {
              wx.showToast({
                title: '成功',
                duration: 3000
              })
              wx.setStorage({
                key: 'user_wxnickname',
                data: wxuser_nickName,
              })
              wx.setStorage({
                key: 'user_wxavatarurl',
                data: wxuser_avatarUrl,
              })
              setTimeout(function () {
                wx.navigateTo({
                  url: '../home/home',
                })
              }, 2050)
            } else {
              console.log(result.data.msg)
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
      } else if (!res.authSetting['scope.userInfo']){
        wx.showToast({
          title: '请授权微信信息',
          image: '/image/wrong.png',
          duration: 4000
        })
        setTimeout(function () {
          wx.navigateTo({
            url: '../signup/signup'
          })
        }, 4050)
      }
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

    // //获取设备页面高度，单位为px
    // console.log('onLoad')
    // var that = this
    // wx.getSystemInfo({
    //   success: function (res) {
    //     that.setData({
    //       height: res.windowHeight
    //     })
    //   }
    // })
   
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