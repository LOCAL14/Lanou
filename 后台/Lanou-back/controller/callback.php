<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/1/21
 * Time: 14:57
 */
//功能：回调函数
//入参：$result  $msg
//出参：success:  $result内容  ;     false: $msg内容
function callback($result = "false",$msg = ""){
    $callback = array(
        "result" => $result,
        "msg" => $msg
    );
    //将callback内容encode编码成字符串传递回前端

    //跨域访问
    header("Access-Control-Allow-Origin:*");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    return json_encode($callback);
}
