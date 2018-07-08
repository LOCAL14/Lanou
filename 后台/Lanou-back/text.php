<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/6/19
 * Time: 下午3:34
 */
require "controller/callback.php";
$phonenumber = $_POST["phonenumber"];
$password = $_POST["password"];
if ($phonenumber == $password){
    echo callback("success","aaa");
    return;
}
echo callback("fail",fail);