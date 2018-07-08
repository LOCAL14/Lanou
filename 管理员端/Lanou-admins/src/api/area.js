import request from '@/utils/request'
import { getToken, setToken, removeToken } from '@/utils/auth'

const session = getToken()

export function getList(listquery) {
    console.log(listquery)
    return request({
        url: '/controller/area.php?action=fetcharea',
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
        url: '/controller/area.php?action=modifyinfo',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,neighborhoodid:id,type:type,adminname:adminname}
    })
} //todo

export function modifydetail(id,temp,adminname) {
    console.log(id,temp,adminname)
    return request({
        url: '/controller/area.php?action=modifydetail',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,neighborhoodid:id,form:temp,adminname:adminname}
    })
} //todo

export function newArea(form,adminname) {
    return request({
        url: '/controller/area.php?action=newarea',
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

