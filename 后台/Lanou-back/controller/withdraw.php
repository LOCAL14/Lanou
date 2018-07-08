<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/1/26
 * Time: 下午5:52
 */
require '../model/withdraw.php';
require '../model/base.php';
global $connect;
$withdraw = new withdraw();
switch ($_GET['action']){
    //发起提现订单
    case 'new':
        $withdraw -> neworder($_POST['session'],$_POST['withdrawvalue']);
        break;

    case 'fetch_history':

        break;

    /*-------------------------以下为管理员面板功能------------------------------*/

    //按条件抓取订单列表
    case 'fetchwithdraworders':
        $withdraw -> fetchwithdraworders($_POST['session'],$_POST['listquery']);
        break;

    //人工提现或关闭此订单或删除此订单，写入log
    case 'modifyinfo':
        $withdraw ->modifyinfo($_POST['session'],$_POST['withdrawid'],$_POST['type'],$_POST['adminname']);
        break;

    //修改提现订单信息，并将操作写入log（具体一点）
    case 'modifydetail':
        $withdraw ->modifydetail($_POST['session'],$_POST['withdrawid'],$_POST['form'],$_POST['adminname']);
        break;


}