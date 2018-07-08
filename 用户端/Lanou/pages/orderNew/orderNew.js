
var sign_diqiu = 0;
var sign_time = 0;
var sign_weight = 0;
var sign_write = 0;

var check_diqiu = 1;
var check_time = 0;
var check_weight = 0;
var check_photo = 0;
var check_upload = 0;

var num_weight = 1;
var num_date = 1;
var num_time = 0;

var tempFilePaths = new Array();
var ossFilePaths = new Array();

Page({

  onShow: function (options) {
    sign_diqiu = 0;
    sign_time = 0;
    sign_weight = 0;
    sign_write = 0;

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

          var temp = new Array()
          var i
          for (i = 0; i < fetch.length; i++) {
            var set = new Object();//temp为一维数组 set是对象
            set.address = fetch[i][4] + fetch[i][6] + fetch[i][8]
            set.contact = fetch[i][9] + ' ' + fetch[i][10]
            set.index = i
            temp[i] = set
          }

          that.setData({
            address: temp,
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

  onUnload: function () {
    num_weight = 1;
    num_date = 1;
    num_time = 0;

    check_diqiu = 1;
    check_time = 0;
    check_weight = 0;
    check_photo = 0;
    check_upload = 0;
  },

  data: {
    address: [],
    selectIndex: 0,

    real_weight: ['', '1', '2', '3', '5', '10', '20', '30', '50', '>50'],
    num: num_weight,

    real_date: ['', '30分钟内', '今天', '明天'],
    date: num_date,

    real_time: ['', '10-12点', '12-14点', '14-16点', '16-18点', '18-20点'],
    time: num_time,

    frame_diqiu_hei: '120rpx',
    frame_diqiu_rot: '0',

    frame_time_hei: '120rpx',
    frame_time_rot: '0',
    frame_time_index_op: '0',
    frame_time_color_1: '#333333',
    frame_time_color_2: '#333333',
    frame_time_color_3: '#333333',
    frame_time_color_4: '#333333',
    frame_time_color_5: '#333333',
    frame_date_hidden_op: '0',
    frame_date_color: '#333333',
    frame_date_margin: '0',
    frame_date_code_hidden_op: '0',
    date_down_op: '1',

    frame_weight_hei: '120rpx',
    frame_weight_rot: '0',
    frame_weight_hidden_op: '0',
    frame_weight_index_op: '0',

    frame_write_hei: '120rpx',
    frame_write_hidden_op: '0',
    frame_write_index_op: '0',
    inputValue: '',

    photo_reset_hidden: 'true',
    photo_hidden_2: 'true',
    photo_hidden_3: 'true',
    real_photo: tempFilePaths,

    boxshadow_diqiu: 'none',
    boxshadow_time: 'none',
    boxshadow_weight: 'none',
    boxshadow_photo: 'none',

    button_backColor: '#e6e6e6',
    button_fontColor: '#333333',


  },

  navigatorToChangeAddress: function () {
    wx.navigateTo({
      url: '../changeaddress/changeaddress?selectIndex=' + this.data.selectIndex,
    })
  },


  writeInput: function (e) {
    this.setData({
      inputValue: e.detail.value
    })
  },



  time: function () {
    if (sign_time == 0) {
      if (num_date == 1) {
        this.setData({
          frame_time_hei: '240rpx',
          boxshadow_time: 'none'
        });
        check_time = 1
      }
      else {
        this.setData({
          frame_time_hei: '500rpx',
        })
        // 不可能出现未选中情况
      }
      this.setData({
        frame_time_rot: '90',
        frame_date_hidden_op: '1',
        frame_time_index_op: '0',
      });
      this.check_button();
      sign_time = 1
    }
    else {
      // 将要闭合态check不参与判定
      if ((num_date == 2 && num_time == 0) || (num_date == 3 && num_time == 0)) {
        this.setData({
          frame_time_color_1: '#ff9800',
          frame_time_color_2: '#ff9800',
          frame_time_color_3: '#ff9800',
          frame_time_color_4: '#ff9800',
          frame_time_color_5: '#ff9800',
        })
      }
      else {
        if (num_date == 1) {
          this.setData({
            frame_time_color_1: '#333333',
            frame_time_color_2: '#333333',
            frame_time_color_3: '#333333',
            frame_time_color_4: '#333333',
            frame_time_color_5: '#333333',
          })
        }
        this.setData({
          frame_time_hei: '120rpx',
          frame_time_rot: '0',
          frame_date_hidden_op: '0',
          frame_time_index_op: '1',
        })
        sign_time = 0
      }
    }
  },

  weight: function () {
    if (sign_weight == 0) {
      this.setData({
        frame_weight_hei: '240rpx',
        frame_weight_rot: '90',
        frame_weight_hidden_op: '1',
        frame_weight_index_op: '0',
        boxshadow_weight: 'none'
      });
      check_weight = 1;
      this.check_button();
      sign_weight = 1
    }
    else {
      this.setData({
        frame_weight_hei: '120rpx',
        frame_weight_rot: '0',
        frame_weight_hidden_op: '0',
        frame_weight_index_op: '1',
      })
      sign_weight = 0
    }
  },

  write: function () {
    if (sign_write == 0) {
      this.setData({
        frame_write_hei: '440rpx',
        frame_write_hidden_op: '1',
        frame_write_index_op: '0',
      })
      sign_write = 1
    }
    else {
      this.setData({
        frame_write_hei: '120rpx',
        frame_write_hidden_op: '0',
        frame_write_index_op: '1',
      })
      sign_write = 0
    }
  },











  date_left: function () {
    if (num_date > 1) {
      num_date -= 1;
      this.setData({
        date: num_date
      })
      this.date_check()
    }
  },

  date_right: function () {
    if (num_date < 3) {
      num_date += 1;
      this.setData({
        date: num_date
      })
      this.date_check()
    }
  },

  date_check: function () {
    num_time = 0;
    if (num_date == 1) {
      this.setData({
        frame_time_hei: '240rpx',
        frame_date_code_hidden_op: '0',
        frame_date_color: '#333333',
        frame_date_margin: '0',
        time: num_time,
        frame_time_color_1: '#333333',
        frame_time_color_2: '#333333',
        frame_time_color_3: '#333333',
        frame_time_color_4: '#333333',
        frame_time_color_5: '#333333',
      });
      check_time = 1;
      this.check_button()
    }
    else {
      this.setData({
        frame_time_hei: '500rpx',
        frame_date_code_hidden_op: '1',
        frame_date_color: '#bbbbbb',
        frame_date_margin: '-20rpx auto 0 auto',
        time: num_time,
        frame_time_color_1: '#333333',
        frame_time_color_2: '#333333',
        frame_time_color_3: '#333333',
        frame_time_color_4: '#333333',
        frame_time_color_5: '#333333',
      });
      check_time = 0;
      this.check_button()
    }

  },





  add: function () {
    if (num_weight < 9) {
      num_weight += 1;
      this.setData({
        num: num_weight
      })
    }
  },

  jian: function () {
    if (num_weight > 1) {
      num_weight -= 1;
      this.setData({
        num: num_weight
      })
    }
  },










  code_time_1: function () {
    num_time = 1;
    check_time = 1;
    this.setData({
      frame_time_color_1: '#2dcb70',
      frame_time_color_2: '#333333',
      frame_time_color_3: '#333333',
      frame_time_color_4: '#333333',
      frame_time_color_5: '#333333',
      time: num_time,
    });
    this.check_button()
  },

  code_time_2: function () {
    num_time = 2;
    check_time = 1;
    this.setData({
      frame_time_color_1: '#333333',
      frame_time_color_2: '#2dcb70',
      frame_time_color_3: '#333333',
      frame_time_color_4: '#333333',
      frame_time_color_5: '#333333',
      time: num_time,
    });
    this.check_button()
  },

  code_time_3: function () {
    num_time = 3;
    check_time = 1;
    this.setData({
      frame_time_color_1: '#333333',
      frame_time_color_2: '#333333',
      frame_time_color_3: '#2dcb70',
      frame_time_color_4: '#333333',
      frame_time_color_5: '#333333',
      time: num_time,
    });
    this.check_button()
  },

  code_time_4: function () {
    num_time = 4;
    check_time = 1;
    this.setData({
      frame_time_color_1: '#333333',
      frame_time_color_2: '#333333',
      frame_time_color_3: '#333333',
      frame_time_color_4: '#2dcb70',
      frame_time_color_5: '#333333',
      time: num_time,
    });
    this.check_button()
  },

  code_time_5: function () {
    num_time = 5;
    check_time = 1;
    this.setData({
      frame_time_color_1: '#333333',
      frame_time_color_2: '#333333',
      frame_time_color_3: '#333333',
      frame_time_color_4: '#333333',
      frame_time_color_5: '#2dcb70',
      time: num_time,
    });
    this.check_button()
  },






  photo_0: function () {
    var that = this;
    if (tempFilePaths[0] == undefined) {
      that.framePhoto()
    }
    else {
      wx.previewImage({
        urls: [tempFilePaths[0]],
      })
    }
  },

  photo_1: function () {
    var that = this;
    if (tempFilePaths[1] == undefined) {
      that.framePhoto()
    }
    else {
      wx.previewImage({
        urls: [tempFilePaths[1]],
      })
    }
  },

  photo_2: function () {
    var that = this;
    if (tempFilePaths[2] == undefined) {
      that.framePhoto()
    }
    else {
      wx.previewImage({
        urls: [tempFilePaths[2]],
      })
    }
  },
  makeid: function makeid() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz";
    for (var i = 0; i < 5; i++)
      text += possible.charAt(Math.floor(Math.random() * possible.length));
    return text;
  },

  framePhoto: function () {
    var that = this;
    wx.chooseImage({
      count: 3,
      sizeType: ['original', 'compressed'],
      sourceType: ['album', 'camera'],
      success: function (res) {
        for (var item of res.tempFilePaths) {
          tempFilePaths.push(item)
          var timestamp = new Date().getTime()
          var newname = timestamp + that.makeid()
          wx.uploadFile({
            url: 'https://iamxz.net/',
            formData: {
              'key': 'uploadimage/' + newname,
              'success_action_status': '200'
            },
            filePath: item,
            name: 'file',
            success: function (res) {
              if (res.statusCode === 200) {
                ossFilePaths.push('https://iamxz.net/uploadimage/' + newname)
              }
              console.log(tempFilePaths)
              console.log(ossFilePaths)
            }
          })






        }

        check_photo = 1;
        that.check_button();
        that.setData({
          real_photo: tempFilePaths,
          boxshadow_photo: 'none',
        })
        if (tempFilePaths[0] != undefined) {
          if (tempFilePaths[1] == undefined) {
            that.setData({
              photo_hidden_2: '',
              photo_hidden_3: 'true',
              photo_reset_hidden: 'true'
            })
          }
          else {
            that.setData({
              photo_hidden_2: '',
              photo_hidden_3: '',
              photo_reset_hidden: 'true'
            });
            if (tempFilePaths[2] != undefined) {
              that.setData({
                photo_reset_hidden: ''
              })
            }
          }
        }

      }
    })
  },
  resetPhoto: function () {
    tempFilePaths = new Array()
    ossFilePaths = new Array()
    this.framePhoto()
  },




  check_button: function () {
    if ((check_diqiu == 1 && check_time == 1) && (check_weight == 1 && check_photo == 1)) {
      this.setData({
        button_backColor: '#2dcb70',
        button_fontColor: 'white',
      })
    }
    else {
      this.setData({
        button_backColor: '#e6e6e6',
        button_fontColor: '#333333',
      })
    }
  },


  button: function () {
    if ((check_diqiu == 1 && check_time == 1) && (check_weight == 1 && check_photo == 1)) {
      //这里接提交
      var that = this
      var session_now = wx.getStorageSync('user_session')

      wx.request({
        url: 'https://www.iamxz.net/controller/order.php?action=neworder',
        method: 'POST',
        header: {
          'content-Type': 'application/x-www-form-urlencoded',
          'Accept': 'application/json'
        },
        data: {
          session: session_now,
          index: this.data.selectIndex,
          appoint_date: num_date,
          appoint_time: num_time,
          evaluate_weight: num_weight,
          remarks: this.data.inputValue?this.data.inputValue:0,
          url1: ossFilePaths[0]?ossFilePaths[0] : 0,
          url2: ossFilePaths[1]?ossFilePaths[1] : 0,
          url3: ossFilePaths[2]?ossFilePaths[2] : 0,
        },
        success: result => {
          if (result.data.result == 'success') {
            wx.redirectTo({
              url: '../ordering/ordering',
            })
          } else if (result.data.msg == 'session已过期') {
            var temp = wxlogin.wxLoginAgain()
            wx.showToast({
              title: '请重试',
              image: '/image/wrong.png',
              duration: 3000
            })
          } else if (result.data.msg == '存在进行中订单') {
            wx.showToast({
              title: '上次订单未完成',
              image: '/image/wrong.png',
              duration: 3000
            })
          } else if (result.data.msg == '无空闲回收员') {
            //todo 弹窗确认预约
            wx.showToast({
              title: '无空闲回收员',
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
    else {
      if (check_diqiu == 0) {
        this.setData({
          boxshadow_diqiu: '0 0 20rpx #ff9800',
        })
      };
      if (check_time == 0) {
        this.setData({
          boxshadow_time: '0 0 20rpx #ff9800',
        })
      };
      if (check_weight == 0) {
        this.setData({
          boxshadow_weight: '0 0 20rpx #ff9800',
        })
      };
      if (check_photo == 0) {
        this.setData({
          boxshadow_photo: '0 0 20rpx #ff9800',
        })
      }
    }
  }





})


