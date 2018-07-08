<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/2/21
 * Time: 23:43
 */
require 'base.php';
require '../controller/callback.php';
require 'session.php';
class dashboard{
    //功能：获取管理员面板的dashboard数据
    //入参：$session
    //出参：success:返回数组：[newuser今日新增用户数,complaint全部投诉订单数,lowrateorder全部低评价订单数（评价星级小于3）,failwithdraw全部失败提现订单数]
    //      false:  session已过期
    public function getdata($session){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
//        获得今日零点的时间戳
        $time = strtotime(date('Ymd'));
        $sql1 = "select * from `user` WHERE registertime > '$time' ";
        $result1 = mysqli_query($connect,$sql1);
        $newuser = mysqli_num_rows($result1);
        $sql2 = "select * from `order` WHERE complaint_time > 0 ";
        $result2 = mysqli_query($connect,$sql2);
        $complaint = mysqli_num_rows($result2);
        $sql3 = "select * from `order` WHERE (rate_star < 3 && rate_star > 0) ";
        $result3 = mysqli_query($connect,$sql3);
        $lowrateorder = mysqli_num_rows($result3);
        $sql4 = "select * from `withdraw_order` WHERE status = 2 ";
        $result4 = mysqli_query($connect,$sql4);
        $failwithdraw = mysqli_num_rows($result4);
        //取aData 近7天每天的低评价订单数（评价星级小于3）
        $time6 = strtotime(date('Ymd')) - 6*24*60*60;
        $time5 = strtotime(date('Ymd')) - 5*24*60*60;
        $time4 = strtotime(date('Ymd')) - 4*24*60*60;
        $time3 = strtotime(date('Ymd')) - 3*24*60*60;
        $time2 = strtotime(date('Ymd')) - 2*24*60*60;
        $time1 = strtotime(date('Ymd')) - 1*24*60*60;
        $sql6_low = "select * from `order` WHERE (order_time > '$time6' && order_time < '$time5') AND (rate_star < 3 && rate_star > 0) ";
        $sql5_low = "select * from `order` WHERE (order_time > '$time5' && order_time < '$time4') AND (rate_star < 3 && rate_star > 0)  ";
        $sql4_low = "select * from `order` WHERE (order_time > '$time4' && order_time < '$time3') AND (rate_star < 3 && rate_star > 0)  ";
        $sql3_low = "select * from `order` WHERE (order_time > '$time3' && order_time < '$time2') AND (rate_star < 3 && rate_star > 0)  ";
        $sql2_low = "select * from `order` WHERE (order_time > '$time2' && order_time < '$time1') AND (rate_star < 3 && rate_star > 0)  ";
        $sql1_low = "select * from `order` WHERE (order_time > '$time1' && order_time < '$time') AND (rate_star < 3 && rate_star > 0)  ";
        $result6_low = mysqli_query($connect,$sql6_low);
        $result5_low = mysqli_query($connect,$sql5_low);
        $result4_low = mysqli_query($connect,$sql4_low);
        $result3_low = mysqli_query($connect,$sql3_low);
        $result2_low = mysqli_query($connect,$sql2_low);
        $result1_low = mysqli_query($connect,$sql1_low);
        $lowrateorder6 = mysqli_num_rows($result6_low);
        $lowrateorder5 = mysqli_num_rows($result5_low);
        $lowrateorder4 = mysqli_num_rows($result4_low);
        $lowrateorder3 = mysqli_num_rows($result3_low);
        $lowrateorder2 = mysqli_num_rows($result2_low);
        $lowrateorder1 = mysqli_num_rows($result1_low);
        $aData = array($lowrateorder6,$lowrateorder5,$lowrateorder4,$lowrateorder3,$lowrateorder2,$lowrateorder1,$lowrateorder);
        //取bData 近7天每天的下单数
        $sql6_new = "select * from `order` WHERE order_time > '$time6' && order_time < '$time5' ";
        $sql5_new = "select * from `order` WHERE order_time > '$time5' && order_time < '$time4' ";
        $sql4_new = "select * from `order` WHERE order_time > '$time4' && order_time < '$time3' ";
        $sql3_new = "select * from `order` WHERE order_time > '$time3' && order_time < '$time2' ";
        $sql2_new = "select * from `order` WHERE order_time > '$time2' && order_time < '$time1' ";
        $sql1_new = "select * from `order` WHERE order_time > '$time1' && order_time < '$time' ";
        $result6_new = mysqli_query($connect,$sql6_new);
        $result5_new = mysqli_query($connect,$sql5_new);
        $result4_new = mysqli_query($connect,$sql4_new);
        $result3_new = mysqli_query($connect,$sql3_new);
        $result2_new = mysqli_query($connect,$sql2_new);
        $result1_new = mysqli_query($connect,$sql1_new);
        $newuser6 = mysqli_num_rows($result6_new);
        $newuser5 = mysqli_num_rows($result5_new);
        $newuser4 = mysqli_num_rows($result4_new);
        $newuser3 = mysqli_num_rows($result3_new);
        $newuser2 = mysqli_num_rows($result2_new);
        $newuser1 = mysqli_num_rows($result1_new);
        $bData = array($newuser6,$newuser5,$newuser4,$newuser3,$newuser2,$newuser1,$newuser);
        $data = array($aData,$bData);
        $array = array($newuser,$complaint,$lowrateorder,$failwithdraw,$data);
        echo (callback("success",$array));
    }



}