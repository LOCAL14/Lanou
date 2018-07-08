<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/1/26
 * Time: 16:21
 */
class smscode
{

    //验证输入的验证码是否正确
    //入参：用户的phonenumber,$thecode
    //出参：success:ture    ;   false:false
    public function verifycode($phonenumber,$thecode){
        global $connect;
        $query2 = "select * from smscode where phonenumber = '$phonenumber' and code = '$thecode' ";
        $result2 = mysqli_query($connect,$query2);
        if (mysqli_num_rows($result2) > 0){
            return true;
        }else{
            return false;
        }
    }



    //功能：发送短信验证码
    //入参：用户的phonenumber
    //出参：success:发送成功    ;   false:失败原因
    public function sendcode($phonenumber){
        global $connect;
        function sendcode($code,$phonenumber){
            $statusStr = array(
                "0" => "短信发送成功",
                "-1" => "参数不全",
                "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
                "30" => "密码错误",
                "40" => "账号不存在",
                "41" => "余额不足",
                "42" => "帐户已过期",
                "43" => "IP地址限制",
                "50" => "内容含有敏感词",
                "51" => "手机号码不正确"
            );
            $content = urlencode("【acampus】您的验证码为".$code."，在2分钟内有效");
            $pwd = md5("yym10295");
            $api = "http://api.smsbao.com/sms?u=piaobodewu&p=".$pwd."&m=".$phonenumber."&c=".$content;
            $result = 1;
            $result =file_get_contents($api) ;
            if($result == 0){
                echo (callback("success","发送成功"));
            }else{
                echo (callback("false","$statusStr[$result]"));
            }
        }

        $sql1 = "select * from smscode where phonenumber = ".$phonenumber." ";
        $sql1_result = mysqli_query($connect,$sql1);
        //判断此手机号是否发过验证码
        if (mysqli_num_rows($sql1_result) > 0){
            //发过
            $now_time_end = time() - 120;
            $sql3 = "select * from `smscode` where phonenumber = ".$phonenumber." and time_end > ".$now_time_end." ";
            $sql3_result = mysqli_query($connect,$sql3);
            //判断是否发送时长是否大于两分钟
            if (mysqli_num_rows($sql3_result) > 0){
                //小于两分钟
                exit(callback("false","2分钟后重试"));
            }else{
                //大于两分钟
                //避免短信服务器bug所以重新发送相同的验证码
                $result4=mysqli_query($connect,"select code from smscode WHERE phonenumber = ".$phonenumber." ");
                $oldcode=mysqli_fetch_row($result4);
                $oold=$oldcode[0];                         //改动啦去掉了引号
                $time_end2 = time();
                $sql3_result = mysqli_query($connect,"UPDATE smscode SET time_end = ".$time_end2." WHERE phonenumber = ".$phonenumber." ");
                $send = sendcode($oold,$phonenumber);
            }
        }else{
            //未发过
            //发验证码
            $code = rand(100000,999999);
            //发送验证码的时间
            $time_end = time();
            $sql2_result = mysqli_query($connect,"INSERT into smscode(phonenumber,code,time_end) VALUES ('".$phonenumber."','".$code."','".$time_end."')");
            $send = sendcode($code,$phonenumber);
        }
    }



}