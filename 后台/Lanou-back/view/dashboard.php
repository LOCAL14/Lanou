<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/2/21
 * Time: 23:38
 */
require '../model/dashboard.php';
global $connect;
$dashboard = new dashboard();
switch ($_GET['action']){
    //获取管理员面板的dashboard数据
    case 'getdata':
        $dashboard -> getdata($_POST['session']);
        break;

}
