import request from '@/utils/request'
import { getToken, setToken, removeToken } from '@/utils/auth'

const session = getToken()

export function getList(listquery) {
    console.log(listquery)
    return request({
        url: '/controller/adminuser.php?action=fetchadminusers',
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
    return request({
        url: '/controller/adminuser.php?action=modifyinfo',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,id:id,type:type,adminname:adminname}
    })
} //todo

export function modifydetail(form) {
    return request({
        url: '/controller/adminuser.php?action=modifydetail',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,form:form}
    })
} //todo

export function newAdminuser(form,adminname) {
    return request({
        url: '/controller/adminuser.php?action=newadminuser',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,form:form,adminname:adminname}
    })
}//todo

