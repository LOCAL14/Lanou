<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/1/26
 * Time: 15:58
 */
require_once '../model/area.php';
global $connect;
$area = new area();
switch ($_GET['action']){

    /*本文件是controller层级的area.php，若只想展示area的内容，移步view/area.php*/

    //注册新小区。并记录此操作。
    case 'newarea':
        $area -> newarea($_POST['session'],$_POST['form'],$_POST['adminname']);
        break;

    //按照条件抓取对应的社区列表
    case 'fetcharea':
        $area -> fetcharea($_POST['session'],$_POST['listquery']);
        break;

    //更改小区状态（0-1）或删除此小区，写入log
    case 'modifyinfo':
        $area -> modifyinfo($_POST['session'],$_POST['neighborhoodid'],$_POST['type'],$_POST['adminname']);
        break;

    //修改小区的信息，并将操作写入log
    case 'modifydetail':
        $area -> modifydetail($_POST['session'],$_POST['neighborhoodid'],$_POST['form'],$_POST['adminname']);
        break;






}