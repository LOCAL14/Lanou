<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/1/26
 * Time: 下午5:52
 */
require '../model/collector.php';
require_once '../model/smscode.php';
require '../model/base.php';
global $connect;

$collector = new collector();
$smscode = new smscode();
switch ($_GET['action']){
    //注册时，读取身份证的信息
    case 'getcardinfo':
        $collector -> getcardinfo($_POST['session'],$_POST['cardurl']);
        break;

    //发送验证码
    case 'sendcode':
        $smscode -> sendcode($_POST['phonenumber']);
        break;

    //按条件抓取回收员列表
    case 'fetchcollector':
        $collector -> fetchcollector($_POST['session'],$_POST['listquery']);
        break;

    //验证用户的短信校验码是否正确，注册新回收员。并将操作写入log
    case 'newcollector':
        $collector -> newcollector($_POST['session'],$_POST['form'],$_POST['adminname']);
        break;

    //更改回收员状态（仅限0/1）或删除回收员，并将操作写入log
    case 'modifyinfo':
        $collector -> modifyinfo($_POST['session'],$_POST['collectorid'],$_POST['type'],$_POST['adminname']);
        break;

    //修改回收员的个人信息，并将操作写入log
    case 'modifydetail':
        $collector -> modifydetail($_POST['session'],$_POST['collectorid'],$_POST['form'],$_POST['adminname']);
        break;
}