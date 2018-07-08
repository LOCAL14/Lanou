import Vue from 'vue'
import Router from 'vue-router'

// in development-env not use lazy-loading, because lazy-loading too many pages will cause webpack hot update too slow. so only in production use lazy-loading;
// detail: https://panjiachen.github.io/vue-element-admin-site/#/lazy-loading

Vue.use(Router)

/* Layout */
import Layout from '../views/layout/Layout'

/**
 * hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
 * alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
 *                                if not set alwaysShow, only more than one route under the children
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: noredirect           if `redirect:noredirect` will no redirct in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    title: 'title'               the name show in submenu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar,
  }
 **/
export const constantRouterMap = [
    {path: '/login', component: () => import('@/views/login/index'), hidden: true},
    {path: '/404', component: () => import('@/views/404'), hidden: true},
    {path: '/rolesinfo', component: () => import('@/views/rolesinfo/index'), hidden: true},

    {
        path: '/',
        component: Layout,
        redirect: '/dashboard',
        name: 'Dashboard',
        hidden: true,
        children: [{
            path: 'dashboard',
            component: () => import('@/views/dashboard/index')
        }]
    },
    {
        path: '/user',
        component: Layout,
        children: [
            {
                path: 'user',
                name: 'User',
                component: () => import('@/views/user/index'),
                meta: {title: '用户', icon: 'user2'}
            }
        ]
    },
    {
        path: '/collector',
        component: Layout,
        redirect: '/collecter/index',
        name: 'collector',
        meta: {title: '回收员', icon: 'user'},
        children: [
            {
                path: 'collectoradd',
                name: 'Collectoradd',
                component: () => import('@/views/collector/add'),
                meta: {title: '添加回收员', icon: 'useradd'}
            },
            {
                path: 'collectorlist',
                name: 'Collectorlist',
                component: () => import('@/views/collector/index'),
                meta: {title: '回收员管理', icon: 'user2'}
            }
        ]
    },
    {
        path: '/order',
        component: Layout,
        redirect: '/order/index',
        name: 'order',
        meta: {title: '订单', icon: 'order'},
        children: [
            {
                path: 'orderlist',
                name: 'Orderlist',
                component: () => import('@/views/order/index'),
                meta: {title: '订单管理', icon: 'order'}
            },
            {
                path: 'ordercheck',
                name: 'Ordercheck',
                component: () => import('@/views/order/check'),
                meta: {title: '快速确认送货', icon: 'check'}
            }
        ]
    },
    {
        path: '/withdraw',
        component: Layout,
        children: [
            {
                path: 'withdraw',
                name: 'Withdraw',
                component: () => import('@/views/withdraw/index'),
                meta: {title: '提现订单', icon: 'bill'}
            }
        ]
    },
    {
        path: '/area',
        component: Layout,
        redirect: '/area/index',
        name: 'area',
        meta: {title: '社区', icon: 'activity'},
        children: [
            {
                path: 'areaadd',
                name: 'Areaadd',
                component: () => import('@/views/area/add'),
                meta: {title: '添加社区', icon: 'add'}
            },
            {
                path: 'arealist',
                name: 'Arealist',
                component: () => import('@/views/area/index'),
                meta: {title: '社区管理', icon: 'activity'}
            }
        ]
    },
    {
        path: '/activity',
        component: Layout,
        redirect: '/activity/index',
        name: 'activity',
        meta: {title: '活动', icon: 'carousel'},
        children: [
            {
                path: 'carousel',
                name: 'Carousel',
                component: () => import('@/views/activity/carousel'),
                meta: {title: '轮播活动管理', icon: 'carousel'}
            },
            {
                path: 'goods',
                name: 'Goods',
                component: () => import('@/views/activity/goods'),
                meta: {title: '特推商品管理', icon: 'goods'}
            }
        ]
    },
    {
        path: '/adminuser',
        component: Layout,
        redirect: '/adminuser/index',
        name: 'adminuser',
        meta: {title: '管理员', icon: 'administer'},
        children: [
            {
                path: 'adminuseradd',
                name: 'Adminuseradd',
                component: () => import('@/views/adminuser/add'),
                meta: {title: '添加管理员', icon: 'useradd'}
            },
            {
                path: 'adminuserlist',
                name: 'Adminuserlist',
                component: () => import('@/views/adminuser/index'),
                meta: {title: '管理员管理', icon: 'user2'}
            }
        ]
    },
    {
        path: '/goods',
        component: Layout,
        redirect: '/goods/index',
        name: 'goods',
        meta: {title: '回收品', icon: 'goods'},
        children: [
            {
                path: 'areaadd',
                name: 'Areaadd',
                component: () => import('@/views/area/add'),
                meta: {title: '添加回收品', icon: 'add'}
            },
            {
                path: 'arealist',
                name: 'Arealist',
                component: () => import('@/views/area/index'),
                meta: {title: '回收品管理', icon: 'goods'}
            }
        ]
    },


    {path: '*', redirect: '/404', hidden: true}
]

export default new Router({
    // mode: 'history', //后端支持可开
    scrollBehavior: () => ({y: 0}),
    routes: constantRouterMap
})

