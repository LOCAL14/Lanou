<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/1/26
 * Time: 15:37
 */
//连接数据库
$host="39.106.19.252";
$username="lanou";
$password="lanou";
$dbname="lanou";
$GLOBALS['connect']=mysqli_connect($host,$username,$password,$dbname);
if (!$connect)
{
    echo("<script language=\"javascript\">alert('数据库连接失败')</script>");
}
