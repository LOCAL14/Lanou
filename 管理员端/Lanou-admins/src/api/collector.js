import request from '@/utils/request'
import { getToken, setToken, removeToken } from '@/utils/auth'

const session = getToken()

export function getList(listquery) {
    console.log(listquery)
    return request({
        url: '/controller/collector.php?action=fetchcollector',
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
        url: '/controller/collector.php?action=modifyinfo',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,collectorid:id,type:type,adminname:adminname}
    })
}

export function modifydetail(id,temp,adminname) {
    console.log(id,temp,adminname)
    return request({
        url: '/controller/collector.php?action=modifydetail',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,collectorid:id,form:temp,adminname:adminname}
    })
}

export function fetchNeighborhood(province,city,district) {
    return request({
        url: '/view/area.php?action=fetchneighborhood',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{province:province,city:city,district:district}
    })
}

export function getcardInfo(cardurl) {
    return request({
        url: '/controller/collector.php?action=getcardinfo',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{session:session,cardurl:cardurl}
    })
}

export function sendcode(phonenumber) {
    return request({
        url: '/controller/collector.php?action=sendcode',
        method: 'POST',
        transformRequest: [function (data) {
            let ret = ''
            for (let it in data) {
                ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
            }
            return ret
        }],
        data:{phonenumber:phonenumber}
    })
}


export function newCollector(form,adminname) {
    return request({
        url: '/controller/collector.php?action=newcollector',
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