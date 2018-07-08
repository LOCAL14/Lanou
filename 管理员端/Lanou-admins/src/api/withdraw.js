import request from '@/utils/request'
import { getToken, setToken, removeToken } from '@/utils/auth'

const session = getToken()

export function getList(listquery) {
    console.log(listquery)
    return request({
        url: '/controller/withdraw.php?action=fetchwithdraworders',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,listquery:listquery}
    })
}

export function modifyinfo(id,type,adminname) {
    console.log(id,type,adminname)
    return request({
        url: '/controller/withdraw.php?action=modifyinfo',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,withdrawid:id,type:type,adminname:adminname}
    })
}

export function modifydetail(id,temp,adminname) {
    console.log(id,temp,adminname)
    return request({
        url: '/controller/withdraw.php?action=modifydetail',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,withdrawid:id,form:temp,adminname:adminname}
    })
}

