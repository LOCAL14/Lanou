<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/1/26
 * Time: 下午6:07
 */
require '../model/homepage.php';
global $connect;
//对象为$user
$homepage = new homepage();
switch ($_GET['action']){
    //查询首页轮播广告的url
    case 'getcarousel':
        $homepage -> ad_fetch();
        break;

    //查询所有废品信息
    case 'getgoods':
        $homepage -> goods_fetch();
        break;

}
