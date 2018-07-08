<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/2/17
 * Time: 上午12:08
 */
require_once '../model/adminuser.php';
global $connect;

$adminuser = new adminuser();

switch ($_GET['action']) {

    //添加新管理员，并将操作写入log（不用具体）
    case 'newadminuser':
        $adminuser -> newadminuser($_POST['session'],$_POST['form'],$_POST['adminname']);
        break;

    //管理员登录
    case 'login':
        $adminuser -> login($_POST['username'],$_POST['password']);
        break;

    //管理员登出
    case 'logout':
        $adminuser -> logout($_POST['session']);
        break;

    //抓取该管理员信息
    case 'getinfo':
        $adminuser -> getinfo($_POST['session']);
        break;

    //按照条件抓取对应的管理员列表;
    case 'fetchadminusers':
        $adminuser -> fetchadminusers($_POST['session'],$_POST['listquery']);
        break;

    //更改管理员状态/删除管理员，计入log
    case 'modifyinfo':
        $adminuser -> modifyinfo($_POST['session'],$_POST['id'],$_POST['type'],$_POST['adminname']);
        break;

    //修改管理员信息
    case 'modifydetail':
        $adminuser -> modifydetail($_POST['session'],$_POST['form']);
        break;

}