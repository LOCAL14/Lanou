//app.js
const app = getApp()

App({
  onLaunch: function () {
    // 展示本地存储能力
    
    var that = this
    // 首次登录
   

    


    //检测微信登录态
    wx.checkSession({
      fail: res=> {
        
        wx.login({
          success: res => {
            //将res.code转换为3rd_session
            //后台转换code为openid，sessionkey；生成对应3rd_session
            wx.request({
              url: 'https://www.iamxz.net/controller/user.php?action=createsession',
              method: 'POST',
              header: {
                'content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
              },
              data: {
                wxuser_code: res.code
              },
              success: result => {
                this.globalData.session = result.data.msg
                wx.setStorage({
                  key: 'user_session',
                  data: this.globalData.session
                })
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
        })

        // 获取用户信息
        wx.getSetting({
          success: res => {
            if (res.authSetting['scope.userInfo']) {
              // 已经授权，可以直接调用 getUserInfo 获取头像昵称，不会弹框
              wx.getUserInfo({
                success: res => {
                  // 可以将 res 发送给后台解码出 unionId
                  this.globalData.userInfo = res.userInfo
                  // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
                  // 所以此处加入 callback 以防止这种情况
                  if (this.userInfoReadyCallback) {
                    this.userInfoReadyCallback(res)
                  }
                }
              })
            }
          }
        })
      },

    })

  },
  globalData: {
    userInfo: null,
    session: 0,
    sessionid: 0,
    balance: 0
    // hasSignup: false
  }
})