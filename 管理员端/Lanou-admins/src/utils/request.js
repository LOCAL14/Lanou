import axios from 'axios'
import { Message, MessageBox } from 'element-ui'
import store from '../store'
import { getToken } from '@/utils/auth'

// 创建axios实例
const service = axios.create({
  baseURL: 'https://www.iamxz.net', // api的base_url process.env.BASE_API
  timeout: 15000 // 请求超时时间
})

// request拦截器
service.interceptors.request.use(config => {
  // if (store.getters.token) {
  //   config.headers['session'] = getToken() // 让每个请求携带自定义token 请根据实际情况自行修改
  // }
   config.headers['content-Type'] = 'application/x-www-form-urlencoded'
  return config
}, error => {
  // Do something with request error
  console.log(error) // for debug
  Promise.reject(error)
})

// respone拦截器
service.interceptors.response.use(
  response => {
    const res = response.data

      if (res.result !== 'success') {

      Message({
        message: res.msg,
        type: 'error',
        duration: 5 * 1000
      })

      if (res.msg === 'session已过期') {
        MessageBox.confirm('你已被登出，可以取消继续留在该页面，或者重新登录', '确定登出', {
          confirmButtonText: '重新登录',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          store.dispatch('FedLogOut').then(() => {
            location.reload()// 为了重新实例化vue-router对象 避免bug
          })
        })
      }
      return Promise.reject('error')
    } else {
      return response.data.msg
    }
  },
  error => {
    console.log('err' + error);// for debug

    Message({
      message: error.message,
      type: 'error',
      duration: 5 * 1000
    });
    return Promise.reject(error)
  }
)

export default service
