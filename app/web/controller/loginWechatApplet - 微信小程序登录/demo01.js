// pages/demo01/demo01.js
Page({

  /**
   * 页面的初始数据
   */
  data: {

  },
  code:function(){
    wx.login({
      success (res) {
        if (res.code) {
          // console.log(res.code);
          //发起网络请求
          wx.request({
            url: 'http://www.ruiwen.com/web/login/loginWechatApplet',
            method: 'get',
            data: {
              code: res.code
            },
            success(data){
              console.log(data.data.data.token);
              // 保存token
              if(data.data.status == 10000){
                wx.setStorage({
                  key: "token",
                  data: data.data.data.token
                });
              }else{
                alert(data.data.msg);
              }
            }
          })
        } else {
          console.log('登录失败！' + res.msg)
        }
      }
    })
  },
  login: function(data){
    console.log(data);
    // console.log(wx.getStorageSync('token'));return false;
    // return false;
    wx.request({
      url: 'http://www.ruiwen.com/web/user/save_info',
      method: 'post',
      header: {
        token:wx.getStorageSync('token')
      },
      data: data.detail.userInfo,
      success(data){
        console.log(data);
      }
    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

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
    console.log('阿卡丽');
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