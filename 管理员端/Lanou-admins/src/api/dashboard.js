import request from '@/utils/request'
import { getToken, setToken, removeToken } from '@/utils/auth'

const session = getToken()

export function getData() {
    return request({
        url: '/view/dashboard.php?action=getdata',
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

