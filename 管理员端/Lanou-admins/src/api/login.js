import request from '@/utils/request'
import { getToken, setToken, removeToken } from '@/utils/auth'

const session = getToken()

export function login(username, password) {
    return request({
        url: '/controller/adminuser.php?action=login',
        method: 'post',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data: {
            username: username,
            password: password
        }
    })
}

export function getInfo() {
    return request({
        url: '/controller/adminuser.php?action=getinfo',
        method: 'post',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data: {session: session}
    })
}

export function logout() {
    return request({
        url: '/controller/adminuser.php?action=logout',
        method: 'post', //todo 登出功能
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data: {session: session}
    })
}
