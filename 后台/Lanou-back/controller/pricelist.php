<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/2/25
 * Time: 22:09
 */
require '../model/pricelist.php';
global $connect;
$pricelist = new pricelist();
switch ($_GET['action']){

    /*本文件是controller层级的pricelist.php，若只想展示pricelist的内容，移步view/pricelist.php*/

    //注册价目表商品。并记录此操作。
    case 'newpricelist':
        $pricelist -> newpricelist($_POST['session'],$_POST['form'],$_POST['adminname']);
        break;

    //更改价目表商品状态（0-1）或删除此商品，写入log
    case 'modifyinfo':
        $pricelist -> modifyinfo($_POST['session'],$_POST['neighborhoodid'],$_POST['type'],$_POST['adminname']);
        break;

    //修改价目表商品的信息，并将操作写入log
    case 'modifydetail':
        $pricelist -> modifydetail($_POST['session'],$_POST['neighborhoodid'],$_POST['form'],$_POST['adminname']);
        break;

}