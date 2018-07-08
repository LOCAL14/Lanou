var province
var city
var district
var neighborhood
var buildingnumber
var detailAddress
var name
var phonenumber
var oldArray = new String
var addressIndex
var hasChanged = false

Page({

  /**
   * 页面的初始数据
   */
  data: {
    region: '',
    neighborhoodArray: '',
    neighborhoodPlaceholder: '小区', //无开通小区时的处理
    neighborhood: '',
    buildingnumberArray: '',
    buildingnumber: '',
    detail: '',
    name: '',
    phonenumber: '',
  },

  //获取选择的Region并赋值给变量，并请求对应的neighborhoodArray
  bindRegionChange: function (e) {
    var that = this
    console.log('Rigion发送选择改变，携带值为', e.detail.value)
    this.setData({
      region: e.detail.value,
      neighborhoodArray: '',
      neighborhoodPlaceholder: '小区',
      neighborhood: '',
      buildingnumberArray: '',
      buildingnumber: ''
    })
    province = e.detail.value[0]
    city = e.detail.value[1]
    district = e.detail.value[2]
    hasChanged = true
    wx.request({
      url: 'https://www.iamxz.net/view/area.php?action=fetchneighborhood',
      method: 'POST',
      header: {
        'content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      data: {
        province: province,
        city: city,
        district: district
      },
      success: result => {
        if (result.data.result == 'success') {
          that.setData({
            neighborhoodArray: result.data.msg,
            neighborhoodPlaceholder: '小区',
          })
        } else if (result.data.msg == '暂无开通小区') {
          that.setData({
            neighborhoodPlaceholder: '暂无开通小区',
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

  //获取选择的Neighborhood并赋值给变量，并请求对应的buildingnumberArray
  bindNeighborhoodChange: function (e) {
    var that = this
    console.log('Neighborhood发送选择改变，携带值为', e.detail.value)
    var temp = this.data.neighborhoodArray[e.detail.value]
    this.setData({
      neighborhood: temp,
      neighborhoodPlaceholder: '小区',
      buildingnumberArray: '',
      buildingnumber: ''
    })
    neighborhood = temp
    hasChanged = true
    wx.request({
      url: 'https://www.iamxz.net/view/area.php?action=fetchbuildingnumber',
      method: 'POST',
      header: {
        'content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      data: {
        neighborhood: temp
      },
      success: result => {
        if (result.data.result == 'success') {
          that.setData({
            buildingnumberArray: result.data.msg,
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

  //获取选择的budingnumber并赋值给变量
  bindBuildingnumberChange: function (e) {
    console.log('Buildingnumber发送选择改变，携带值为', e.detail.value)
    var temp = this.data.buildingnumberArray[e.detail.value]
    this.setData({
      buildingnumber: temp
    })
    buildingnumber = temp
    hasChanged = true
  },


  //获取输入信息，并赋值给变量
  detailAddressSubmit: function (a) {
    detailAddress = a.detail.value
    hasChanged = true
  },

  nameSubmit: function (a) {
    name = a.detail.value
    hasChanged = true
  },

  phonenumberSubmit: function (a) {
    phonenumber = parseInt(a.detail.value)
    hasChanged = true
  },

  modifyAddress: function () {
    var app = getApp()
    var that = this
    var session_now = wx.getStorageSync('user_session')
    var array = new Array()
    if (hasChanged) {
      if (!buildingnumber) {
        wx.showToast({
          title: '请选择小区楼号',
          image: '/image/wrong.png',
          duration: 3000
        })
      } else if (!detailAddress) {
        wx.showToast({
          title: '请填写详细地址',
          image: '/image/wrong.png',
          duration: 3000
        })
      } else if (!name) {
        wx.showToast({
          title: '请填写联系人',
          image: '/image/wrong.png',
          duration: 3000
        })
      } else if (!phonenumber) {
        wx.showToast({
          title: '请填写联系电话',
          image: '/image/wrong.png',
          duration: 3000
        })
      } else {
        array[0] = province
        array[1] = city
        array[2] = district
        array[3] = neighborhood
        array[4] = buildingnumber
        array[5] = detailAddress
        array[6] = name
        array[7] = phonenumber
        array = JSON.stringify(array)
      }
      wx.request({
        url: 'https://www.iamxz.net/controller/user.php?action=modifyaddress',
        method: 'POST',
        header: {
          'content-Type': 'application/x-www-form-urlencoded',
          'Accept': 'application/json'
        },
        data: {
          session: session_now,
          address_array: array,
          index: addressIndex
        },
        success: result => {
          if (result.data.result == 'success') {
            wx.navigateBack({
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
    } else {
      wx.showToast({
        title: '地址没有变化',
        image: '/image/wrong.png',
        duration: 3000
      })
    }
  },

  deleteAddress:function(){
    var app = getApp()
    var that = this
    var session_now = wx.getStorageSync('user_session')
    
    wx.request({
      url: 'https://www.iamxz.net/controller/user.php?action=deleteaddress',
      method: 'POST',
      header: {
        'content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      data: {
        session: session_now,
        index: addressIndex
      },
      success: result => {
        if (result.data.result == 'success') {
          wx.navigateBack({
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

    //得到所选地址的信息和index 进行初始赋值
    addressIndex = options.addressIndex
    oldArray = options.oldAddress
    oldArray = oldArray.split(",")
    province = oldArray[0]
    city = oldArray[1]
    district = oldArray[2]
    neighborhood = oldArray[4]
    buildingnumber = oldArray[6]
    detailAddress = oldArray[8]
    name = oldArray[9]
    phonenumber = oldArray[10]
    this.setData({
      region: [province, city, district],
      neighborhood: neighborhood,
      buildingnumber: buildingnumber,
      detail: detailAddress,
      name: name,
      phonenumber: phonenumber
    })
    //初始赋值neighborhoodArray
    wx.request({
      url: 'https://www.iamxz.net/view/area.php?action=fetchneighborhood',
      method: 'POST',
      header: {
        'content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      data: {
        province: province,
        city: city,
        district: district
      },
      success: result => {
        if (result.data.result == 'success') {
          that.setData({
            neighborhoodArray: result.data.msg,
            neighborhoodPlaceholder: '小区',
          })
        } else if (result.data.msg == '暂无开通小区') {
          that.setData({
            neighborhoodPlaceholder: '暂无开通小区',
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
    //初始赋值buildingArray
    wx.request({
      url: 'https://www.iamxz.net/view/area.php?action=fetchbuildingnumber',
      method: 'POST',
      header: {
        'content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      data: {
        neighborhood: neighborhood
      },
      success: result => {
        if (result.data.result == 'success') {
          that.setData({
            buildingnumberArray: result.data.msg,
          })
        } else {
          wx.showToast({
            title: result.data.msg,
            image: '/image/wrong.png',
            duration: 3000
          })
        }
      },
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