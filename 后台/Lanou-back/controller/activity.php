<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/2/23
 * Time: 13:43
 */
require '../model/activity.php';
global $connect;
$activity = new activity();
switch ($_GET['action']){

    //新建首页轮播图活动
    case 'newactivity':
        $activity -> newactivity($_POST['session'],$_POST['form'],$_POST['adminname']);
        break;

    //抓取全部‘首页轮播图’活动，返回数据表里的全部信息
    case 'fetchactivities':
        $activity -> fetchactivities($_POST['session']);
        break;

    //更改首页轮播图状态（0-1）或删除此首页轮播图，写入log
    case 'modifyinfo':
        $activity -> modifyinfo($_POST['session'],$_POST['id'],$_POST['type'],$_POST['adminname']);
        break;

    //修改首页轮播图的信息，并将操作写入log
    case 'modifydetail':
        $activity -> modifydetail($_POST['session'],$_POST['id'],$_POST['form'],$_POST['adminname']);
        break;

    //抓取全部‘首页特推商品’活动，返回数据表里的全部信息
    case 'fetchactivities_goods':
        $activity -> fetchactivities_goods($_POST['session']);
        break;

    //修改首页特推商品的信息，并将操作写入log（不用具体）
    case 'modifydetail_goods':
        $activity -> modifydetail_goods($_POST['session'],$_POST['id'],$_POST['form'],$_POST['adminname']);
        break;

}
