<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/1/26
 * Time: 下午5:47
 */
require_once 'session.php';
require_once 'base.php';
require_once '../controller/callback.php';
class withdraw{
    //功能：发起提现订单
    //入参：用户$session
    //出参：success: 提现已发起  ;    false:金额超过余额    false:session已过期
    public function neworder($session,$withdraw_money){
        global $connect;
        $apply_time = time();

        $s = new session(); //没有Require文件
        $openid = $s ->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $money = 0;
        $result1=mysqli_query($connect,"select userid,phonenumber,money from `user` WHERE openid = '$openid' ");
        $array=mysqli_fetch_row($result1);
        $userid = $array[0];
        $phonenumber = $array[1];
        $money=$array[2];
        //判断提现金额是否大于总金额
        if ($withdraw_money < $money){
            //符合提现条件
            //发起该提现订单
            $sql1 = "insert into withdraw_order (userid,openid,phonenumber,withdraw_money,apply_time) values ('$userid','$openid','$phonenumber','$withdraw_money','$apply_time')";
            mysqli_query($connect,$sql1);
            $newmoney = 0;
            $newmoney = $money - $withdraw_money;
            $sql2 = "UPDATE user SET money = '$newmoney' WHERE openid = '$openid'";
           mysqli_query($connect,$sql2);
            echo callback("success","提现已发起");
        }else{
            //不符合条件条件
            echo callback("false","金额超过余额");
        }
    }


    /*-------------------------以下为管理员面板功能------------------------------*/

    //功能：按照条件抓取对应的提现订单列表;
    //入参：用户$session，$listquery（查询条件json数组）
    //出参：success:数组[items,total]     ；   false:session已过期
    public function fetchwithdraworders($session, $listquery)
    {
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql1 = "select * from `withdraw_order`";
        $result1 = mysqli_query($connect, $sql1);


        $listquery = json_decode($listquery);
        $querypage = $listquery->page;
        $querylimit = $listquery->limit;
        $querycity = $listquery->city;
        $querytitle = $listquery->title;
        $queryvalue = $listquery->value;

        if (mysqli_num_rows($result1) > 0) {
            //存在用户
            if ($querytitle == 'phonenumber') {
                $where1 = ' and '. $querytitle . ' = ' . $queryvalue;
            } else if ($querytitle == 'withdrawid') {
                $where1 = ' and withdrawid = ' . "'$queryvalue'";
            }else {
                $where1 = '';
            }

            $select = 'withdrawid,userid,phonenumber,withdraw_money,apply_time,success_time,status,
            error_code,remark';
            $sql2 = "SELECT $select FROM `withdraw_order` 
                      WHERE 1=1 $where1  ORDER BY withdrawid ASC"; //顺序改动
            $result2 = mysqli_query($connect, $sql2);
            $items = array();
            while ($data = mysqli_fetch_assoc($result2)) {
                $data['apply_time'] = date("Y-m-d H:i", $data['apply_time']);
                array_push($items, $data);
            }
            $result['total'] = count($items);
            $items = array_chunk($items, $querylimit);
            $result['items'] = $items[$querypage - 1];
            echo callback("success", $result);
        } else {
            //无用户
            echo callback("false", "未查询到提现订单");
        }
    }

    //功能：人工提现或关闭此订单或删除此订单，写入log
    //入参：$session：管理员的session；  $withdrawid：提现订单的id；  $type：操作类型(可选：manual/changestatus/delete)；  $adminname：管理员的name
    //出参：success: 操作成功；      false:无此提现订单  session已过期
    public function modifyinfo($session,$withdrawid,$type,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql = "select * from `withdraw_order` WHERE withdrawid = '$withdrawid' ";
        $result = mysqli_query($connect,$sql);
        if (mysqli_num_rows($result) == 0){
            exit(callback("false","无此提现订单"));
        }
        $data = 'withdraw_order';
        $time = date("Y-m-d H:i:s",time());
        switch ($type){
            //人工提现
            case 'manual':
                $sql1 = "update `withdraw_order` set status = 2 WHERE withdrawid = '$withdrawid' ";
                $result1 = mysqli_query($connect,$sql1);
                $type = "update";
                $description = "人工提现订单。"."提现订单id："."$withdrawid";
                $sql1_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                $result1_m = mysqli_query($connect,$sql1_m);
                echo (callback("success","操作成功"));
                break;
            //关闭此提现订单
            case 'changestatus':
                $sql1 = "update `withdraw_order` set status = 0 WHERE withdrawid = '$withdrawid' ";
                $result1 = mysqli_query($connect,$sql1);
                $type = "update";
                $description = "关闭此提现订单。"."提现订单id："."$withdrawid";
                $sql1_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                $result1_m = mysqli_query($connect,$sql1_m);
                echo (callback("success","操作成功"));
                break;
            //删除此提现订单
            case 'delete':
                $sql2 = "delete from `withdraw_order` WHERE withdrawid = '$withdrawid' ";
                $result2 = mysqli_query($connect,$sql2);
                $description = "删除此提现订单。"."提现订单id："."$withdrawid";
                $sql2_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                $result2_m = mysqli_query($connect,$sql2_m);
                echo (callback("success","操作成功"));
                break;
        }
    }


    //功能：修改提现订单信息，并将操作写入log（具体一点）
    //入参：$session：管理员的session；
    //      $withdrawid：提现订单的id；
    //      $form:形式与SELECT withdrawid,userid,phonenumber,withdraw_money,apply_time,success_time,status,error_code,remark FROM withdraw 的mysqli_fetch_assoc结果相同，是一个键值对形式的数组；
    //      $adminname：管理员的name
    //出参：success: 操作成功；      false:无此提现订单  session已过期
    public function modifydetail($session,$withdrawid,$form,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql1 = "select * from `withdraw_order` WHERE withdrawid = '$withdrawid' ";
        $result1 = mysqli_query($connect,$sql1);
        if (mysqli_num_rows($result1) == 0){
            exit(callback("false","无此提现订单"));
        }
        $form = json_decode($form);
        $remark = $form -> remark;
        $sql2 = "select remark from `withdraw_order` WHERE withdrawid = '$withdrawid' ";
        $result2 = mysqli_query($connect,$sql2);
        $old_remark = mysqli_fetch_row($result2);
        $old_remark = $old_remark[0];
        $sql3 = "update `withdraw_order` set remark = '$remark' WHERE withdrawid = '$withdrawid' ";
        $result3 = mysqli_query($connect,$sql3);
        $time = date("Y-m-d H:i:s",time());
        $data = 'withdraw_order';
        $type = "update";
        $description = "修改提现订单信息。"."提现订单id："."$withdrawid"."。原订单备注："."$old_remark"."。现订单备注："."$remark";
        $sql5_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
        $result5_m = mysqli_query($connect,$sql5_m);
        echo (callback("success","操作成功"));
    }

}