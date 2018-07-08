import request from '@/utils/request'
import { getToken, setToken, removeToken } from '@/utils/auth'

const session = getToken()

export function getList() {
    return request({
        url: '/controller/activity.php?action=fetchactivities',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session}
    })
}

export function modifyinfo(id,type,adminname) {
    console.log(id,type,adminname)
    return request({
        url: '/controller/activity.php?action=modifyinfo',
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
}

export function modifydetail(id,temp,adminname) {
    console.log(id,temp,adminname)
    return request({
        url: '/controller/activity.php?action=modifydetail',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,id:id,form:temp,adminname:adminname}
    })
}

export function newActivity(form,adminname) {
    return request({
        url: '/controller/activity.php?action=newactivity',
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
}

export function getList_goods() {
    return request({
        url: '/controller/activity.php?action=fetchactivities_goods',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session}
    })
}


export function modifydetail_goods(id,temp,adminname) {
    console.log(id,temp,adminname)
    return request({
        url: '/controller/activity.php?action=modifydetail_goods',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,id:id,form:temp,adminname:adminname}
    })
}
