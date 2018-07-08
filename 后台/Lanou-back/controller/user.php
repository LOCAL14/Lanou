<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/1/26
 * Time: 15:58
 */
require_once '../model/user.php';
global $connect;

//对象$user 为类user()的一个具体化实例
$user = new user();
$smscode = new smscode();
switch ($_GET['action']){
    //生成每个用户的唯一标识并给前端传回session值
    case 'createsession':
        $user -> createsession($_POST['wxuser_code']);      //方法
        break;

    //发送验证码
    case 'sendcode':
        $smscode -> sendcode($_POST['phonenumber']);
        break;

    //验证用户是否注册 未注册则插入数据库
    case 'new':
        $user -> newuser($_POST['session'],$_POST['phonenumber'],$_POST['thecode'],$_POST['wxnickname'],$_POST['wxavatarurl']);
        break;

    //查看用户是否注册，如果注册则获取用户的个人信息
    case 'getuserinfo':
        $user -> getuserinfo($_POST['session']);
        break;

    //查询用户余额
    case 'getusermoney':
        $user -> getusermoney($_POST['session']);
        break;

    //查询用户手机尾号
    case 'getphonenumber':
        $user -> getphonenumber($_POST['session']);
        break;

    //发送验证码，并对比新旧手机号是否相同
    case 'sendcode_changephone':
        $equal = $user -> compare_phonenumber($_POST['session'],$_POST['phonenumber']);
        if($equal){
            echo callback('false','新旧手机号相同');
        }else{
            $smscode -> sendcode($_POST['phonenumber']);
        }
        break;

    //更改用户手机号
    case 'changephone':
        $user -> changephone($_POST['session'],$_POST['phonenumber'],$_POST['code']);
        break;

    //将用户地址信息的数组插入到数据库user表中  $array{省，市，区，小区，楼号，详细地址，联系人，手机号}
    case 'addaddress':
        $user -> addaddress($_POST['session'],$_POST['address_array']);
        break;

    //查询用户全部地址
    case 'getaddress':
        $user -> getaddress($_POST['session']);
        break;

    //修改用户某一地址
    case 'modifyaddress':
        $user -> modifyaddress($_POST['session'],$_POST['address_array'],$_POST['index']);
        break;

    //删除用户某一地址
    case 'deleteaddress':
        $user -> deleteaddress($_POST['session'],$_POST['index']);
        break;

    /*-------------------------以下为管理员面板功能------------------------------*/

    //按照条件抓取对应的用户列表
    case 'fetchusers':
        $user -> fetchusers($_POST['session'],$_POST['listquery']);
        break;

    //更改用户状态或删除用户，并将操作写入数据表
    case 'modifyinfo':
        $user -> modifyinfo($_POST['session'],$_POST['userid'],$_POST['type'],$_POST['adminname']);
        break;

}