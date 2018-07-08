<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/2/7
 * Time: 上午1:13
 */
require '../model/area.php';
global $connect;
$area = new area();
switch ($_GET['action']){

    /*本文件是view层级的area.php，若涉及到对area本身做修改，移步controller/area.php*/

    //通过省市区抓取已开通服务小区的名称数组
    case 'fetchneighborhood':
        $area -> fetchneighborhood($_POST['province'],$_POST['city'],$_POST['district']);
        break;

    //通过小区名称抓取小区楼号情况数组
    case 'fetchbuildingnumber':
        $area -> fetchbuildingnumber($_POST['neighborhood']);
        break;

}