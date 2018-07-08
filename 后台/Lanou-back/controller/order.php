<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/1/26
 * Time: 下午5:32
 */
require '../model/order.php';
global $connect;
$order = new order();
switch ($_GET['action']){
    //下订单
    case 'neworder':
        $order -> neworder($_POST['session'],$_POST['index'],$_POST['appoint_date'],$_POST['appoint_time'],$_POST['evaluate_weight'],$_POST['remarks'],$_POST['url1'],$_POST['url2'],$_POST['url3']);
        break;

    //评价订单
    case 'evaluateorder':
        $order -> evaluateorder($_POST['session'],$_POST['orderid'],$_POST['rate_star'],$_POST['rate_text']);
        break;

    //取消订单
    case 'cancelorder':
        $order -> cancelorder($_POST['session'],$_POST['orderid']);
        break;

    //投诉订单
    case 'complaintorder':
        $order -> complaintorder($_POST['session'],$_POST['orderid'],$_POST['complaint_text']);
        break;

    //查询用户正在进行中的订单信息并返回信息
    case 'getordering':
        $order -> getordering($_POST['session']);
        break;

    //查询用户历史订单
    case 'fetch_history':
        $demand = 'status = 0 or status = 3 or status = 4 or status = 5';
        $order -> queryorders($_POST['session'],$demand);
        break;

    //按条件抓取订单列表
    case 'fetchorders':
        $order -> fetchorders($_POST['session'],$_POST['listquery']);
        break;

     //抓取该历史订单的订单详情
    case 'getorderinfo':
        $order -> getorderinfo($_POST['session'],$_POST['orderid']);
        break;

    /*-------------------------以下为管理员面板功能------------------------------*/

    //改派回收员或关闭此订单或删除此订单
    case 'modifyinfo':
        $order -> modifyinfo($_POST['session'],$_POST['orderid'],$_POST['type'],$_POST['adminname']);
        break;

    //修改订单信息，并将操作写入log
    case 'modifydetail':
        $order -> modifydetail($_POST['session'],$_POST['orderid'],$_POST['form'],$_POST['adminname']);
        break;

    //确认订单已送货
    case 'checkorder':
        $order -> checkorder($_POST['session'],$_POST['orderid'],$_POST['adminname']);
        break;
}