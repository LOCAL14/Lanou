//商品列表fetch
var that = this
var fetch
var temp
//data array : '' 
wx.request({
  url: 'https://www.iamxz.net/controller/homepage_fetch.php',
  method: 'POST',
  success: function (result) {
    fetch = result.data.msg
    console.log(fetch)
    temp = fetch[0].name
    console.log(temp)
    that.setData({
      array: fetch
    })
  }
})
  //html 分两列
  // < template wx: if="{{index%2 === 0}}" is= "courseLeft" data= "{{...item}}" > </template>
  //   < template wx:else is = "courseRight" data= "{{...item}}" > 
  //</template>




  // html 写一行
  // <block  wx: for='{{array}}' > 
  //   <text > 1 < /text>
  //   < template is= "msgItem" data= "{{...item}}" />
  //   </block>

  //   < template name= "msgItem" >
  //     <view>
  //     <text>price: { { unitprice } } </text>
  //       < text > url: { { url } } </text>
  //         < /view>
  //         < /template>