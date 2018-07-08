const app = getApp()
var that = this

Page({
  data: {
    imgUrls: [],
    indicatorDots: true,
    autoplay: true,
    interval: 3000,
    duration: 1000,
    url1: '',
    url2: '',
    url3: '',
    unitprice1: '',
    unitprice2: '',
    unitprice3: '',
    home_color: '#2dcb70',
    home_z: '50',
    home_blur: 'blur(0)',
    home_op: '1',
    recircle_z: '-2',
    sec_ball_color: '#f5f5f5',
    recircle_color: 'black',
    recircle_op: '0',
    me_z: '-3',
    me_color: 'black',
    me_blur: 'blur(0)',
    avatorUrl: '',
    name: '',
    phonenumber: '',
    hasSignup: false
  },







  home_index: function () {
    if (this.data.home_z == '-1' || this.data.home_z == '-3') {
      this.setData({
        home_z: '50',
        recircle_z: '-2',
        me_z: '-3',
        home_color: '#2dcb70',
        home_blur: 'blur(0)',
        home_op: '1',
        sec_ball_color: '#f5f5f5',
        recircle_color: 'black',
        recircle_op: '0',
        me_color: 'black',
        me_blur: 'blur(0)'
      })
    }
  },

  recircle_index: function () {
    if (this.data.recircle_z == '-2') {
      if (this.data.me_z == '-3') {
        this.setData({
          home_z: '-1',
          home_blur: 'blur(8px)'
        })
      }
      if (this.data.home_z == '-3') {
        this.setData({
          me_z: '-1',
          home_op: '0',
          me_blur: 'blur(8px)'
        })
      }
      this.setData({
        recircle_z: '50',
        home_color: 'black',
        sec_ball_color: '#2dcb70',
        recircle_color: 'white',
        recircle_op: '1',
        me_color: 'black',
      })
    }
  },

  me_index: function () {
    if (this.data.me_z == '-1' || this.data.me_z == '-3') {
      this.setData({
        home_z: '-3',
        recircle_z: '-2',
        me_z: '50',
        home_color: 'black',
        home_blur: 'blur(0)',
        sec_ball_color: '#f5f5f5',
        recircle_color: 'black',
        recircle_op: '0',
        me_color: '#2dcb70',
        me_blur: 'blur(0)',
      })
    }
  },





  rank_list: function () {
    wx.navigateTo({
      url: '../rank_list/rank_list',
    })
  },
  helpcenter: function () {
    wx.navigateTo({
      url: '../helpcenter/helpcenter',
    })
  },
  wallet: function () {
    wx.navigateTo({
      url: '../me_wallet/me_wallet',
    })
  },
  order: function () {
    wx.navigateTo({
      url: '../orderNew/orderNew',
    })
  },
  ordering: function () {
    wx.navigateTo({
      url: '../ordering/ordering',
    })
  },
  historyorder: function () {
    wx.navigateTo({
      url: '../historyorder/historyorder',
    })
  },
  changeaddress: function () {
    wx.navigateTo({
      url: '../changeaddress/changeaddress',
    })
  },
  change_phone: function () {
    wx.navigateTo({
      url: '../change_phone/change_phone',
    })
  },
  price_list: function () {
    wx.navigateTo({
      url: '../Price_list/Price_list',
    })
  },


  haveSignup: function () {
    if (!this.data.hasSignup) {
      wx.navigateTo({
        url: '../signup/signup',
      })
    }
  },





















  onLoad: function () {
    var that = this
    var fetch
    var temp

    //抓取主页轮播图
    wx.request({
      url: 'https://www.iamxz.net/view/homepage.php?action=getcarousel',
      method: 'POST',
      success: function (result) {
        fetch = result.data.msg
        console.log(fetch)
        that.setData({
          imgUrls: fetch
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

    //抓取主页价目表
    wx.request({
      url: 'https://www.iamxz.net/view/homepage.php?action=getgoods',
      method: 'POST',
      success: function (result) {
        fetch = result.data.msg
        console.log(fetch)
        temp = fetch[0].name
        that.setData({
          url1: fetch[0].url,
          url2: fetch[1].url,
          url3: fetch[2].url,
          unitprice1: fetch[0].unitprice,
          unitprice2: fetch[1].unitprice,
          unitprice3: fetch[2].unitprice,
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


    //验证用户是否注册，并获取微信头像
    var session_now = wx.getStorageSync('user_session')
    wx.request({
      url: 'https://www.iamxz.net/controller/user.php?action=getuserinfo',
      method: 'POST',
      header: {
        'content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      data: {
        session: session_now,
      },
      success: function (result) {
        fetch = result.data.msg
        console.log(fetch)
        if (result.data.result == 'success') {
          that.setData({
            avatorUrl: fetch[0],
            name: fetch[1],
            phonenumber: fetch[2],
            hasSignup: true
          })
        } else if (result.data.msg == '用户未注册') {
          that.setData({
            avatorUrl: '',
            name: '未注册',
            phonenumber: '',
          })
        } else if (result.data.msg == '用户已停用'){
          that.setData({
            avatorUrl: '',
            name: '用户已停用',
            phonenumber: '',
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
  onShow: function () {
    var that = this
    var fetch
    var temp

    //验证用户是否注册，并获取微信头像
    var session_now = wx.getStorageSync('user_session')



  }

})