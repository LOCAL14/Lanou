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

    /*本文件是view层级的pricelist.php，若涉及到对pricelist本身做修改，移步controller/pricelist.php*/

    //抓取除status的所有价目表信息（客户端），只抓status = 1的
    case 'fetchpricelist':
        $pricelist -> fetchpricelist();
        break;

    //抓取除status的所有价目表信息（管理员端），抓所有status
    case 'fetchpricelist_m':
        $pricelist -> fetchpricelist_m();
        break;

}