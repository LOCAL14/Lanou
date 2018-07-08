<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/2/23
 * Time: 13:43
 */
require_once 'base.php';
require_once '../controller/callback.php';
require_once 'session.php';
class activity{

    //功能：新建首页轮播图活动
    //入参：$session：管理员的session；
    //      $form ：数组[name ,url ,activity_url, activity_introduction]；
    //      $adminname：管理员的name
    //出参：success: 操作成功；      false: session已过期
    public function newactivity($session,$form,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $add_time = date("Y-m-d H:i:s",time());
        $form = json_decode($form);
        $name = $form -> name;
        $url = $form -> url;
        $activity_url = $form -> activity_url;
        $activity_introduction = $form -> activity_introduction;
        $sql = "select * from `homead_goods` ";
        $result = mysqli_query($connect,$sql);
        $all_id = mysqli_num_rows($result);
        $id = $all_id + 1;
        $sql1 = "insert into 
                 `homead_goods`(`id`,`name`,`url`,`activity_url`,`activity_introduction`,`time`) 
                 VALUES 
                 ('$id','$name','$url','$activity_url','$activity_introduction','$add_time')";
        $result1 = mysqli_query($connect,$sql1);
        $type = "add";
        $data = 'homead_goods';
        $time = date("Y-m-d H:i:s",time());
        $description = "新建首页轮播图活动。"."轮播图编号："."$id";
        $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
        $result3_m = mysqli_query($connect,$sql3_m);
        echo (callback("success","操作成功"));
    }



    //功能：抓取全部‘首页轮播图’活动，返回数据表里的全部信息
    //入参：$session：管理员的session
    //出参：success: 操作成功；      false: session已过期
    public function fetchactivities($session){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql_n = "select * from `homead_goods` ";
        $result_n = mysqli_query($connect,$sql_n);
        $number = mysqli_num_rows($result_n);
        $array = array();
        $i = 1;
        while ($number>=$i){
            $data = mysqli_fetch_assoc($result_n);
            array_push($array,$data);
            $i++;
        }
        echo (callback("success",$array));
    }



    //功能：更改首页轮播图状态（0-1）或删除此首页轮播图，写入log
    //入参：$session：管理员的session；  $id：首页轮播图的id；  $type：操作类型(可选：changestatus/delete)；  $adminname：管理员的name
    //出参：success: 操作成功；      false:无此轮播图  session已过期
    public function modifyinfo($session,$id,$type,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql = "select * from `homead_goods` WHERE id = '$id' ";
        $result = mysqli_query($connect,$sql);
        if (mysqli_num_rows($result) == 0){
            exit(callback("false","无此轮播图"));
        }
        $data = 'homead_goods';
        $time = date("Y-m-d H:i:s",time());
        $sql2 = "select `id`,`activity_introduction`,`status` from `homead_goods` WHERE id = '$id' ";
        $result2 = mysqli_query($connect,$sql2);
        $array = mysqli_fetch_row($result2);
        $id = $array[0];
        $activity_introduction = $array[1];
        $status = $array[2];
        switch ($type){
            //更改首页轮播图状态（0-1）
            case 'changestatus':
                $type = "update";
                if ($status == 1){
                    $sql3 = "update `homead_goods` set status = 0 WHERE id = '$id' ";
                    $result3 = mysqli_query($connect,$sql3);
                    $description = "更改首页轮播图status为0。"."轮播图编号："."$id"."，轮播图活动简介："."$activity_introduction";
                    $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                    $result3_m = mysqli_query($connect,$sql3_m);
                    echo (callback("success","操作成功"));
                }else{
                    $sql4 = "update `homead_goods` set status = 1 WHERE id = '$id' ";
                    $result4 = mysqli_query($connect,$sql4);
                    $description = "更改首页轮播图status为1。"."轮播图编号："."$id"."，轮播图活动简介："."$activity_introduction";
                    $sql4_m = "INSERT into log(data,description,type,id,time) VALUES ('$data','$description','$type','$adminname','$time')";
                    $result4_m = mysqli_query($connect,$sql4_m);
                    echo (callback("success","操作成功"));
                }
                break;
            //删除此首页轮播图
            case 'delete':
                $sql5 = "delete from `homead_goods` WHERE id = '$id' ";
                $result5 = mysqli_query($connect,$sql5);
                $description = "删除此首页轮播图。"."轮播图编号："."$id"."，轮播图活动简介："."$activity_introduction";
                $sql5_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                $result5_m = mysqli_query($connect,$sql5_m);
                //id实现上移补全
                $sql_n = "SELECT * FROM `homead_goods` WHERE 1";
                $result_n = mysqli_query($connect,$sql_n);
                $number = mysqli_num_rows($result_n);
                $number = $number - $id + 1 ;
                for (;$number > 0;$number--){
                    $old_id = $id + 1;
                    $sql = "update `homead_goods` set id = '$id' WHERE id = '$old_id' ";
                    $result = mysqli_query($connect,$sql);
                    $id++;
                }
                echo (callback("success","操作成功"));
                break;
        }
    }



    //功能：修改首页轮播图的信息，并将操作写入log
    //入参：$session：管理员的session；
    //      $id：首页轮播图的id；
    //      $form: 一个键值对形式的数组，键分别为： `id`, `name`, `url`, `activity_url`, `activity_introduction`, `status`
    //      $adminname：管理员的name
    //出参：success: 操作成功；      false:无此轮播图  session已过期
    public function modifydetail($session,$id,$form,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql = "select * from `homead_goods` WHERE id = '$id' ";
        $result = mysqli_query($connect,$sql);
        if (mysqli_num_rows($result) == 0){
            exit(callback("false","无此轮播图"));
        }
        $add_time = date("Y-m-d H:i:s",time());
        $form = json_decode($form);
        //传回的值
        $name = $form -> name;
        $url = $form -> url;
        $activity_url = $form -> activity_url;
        $activity_introduction = $form -> activity_introduction;
        $status = $form -> status;
        //数据库查到的值
        $sql1 = "select name,url,activity_url,activity_introduction,status from `homead_goods` WHERE id = '$id' ";
        $result1 = mysqli_query($connect,$sql1);
        $array1 = mysqli_fetch_row($result1);
        $old_name = $array1[0];
        $old_url = $array1[1];
        $old_activity_url = $array1[2];
        $old_activity_introduction = $array1[3];
        $old_status = $array1[4];
        if ($name == $old_name
            && $url == $old_url
            && $activity_url == $old_activity_url
            && $activity_introduction == $old_activity_introduction
            && $status == $old_status
        ){
            exit(callback("success","操作成功"));
        }else{
            $sql3 = "update `homead_goods` 
                     set 
                     name = '$name',url = '$url',activity_url = '$activity_url',activity_introduction = '$activity_introduction',status = '$status' ,time = '$add_time'
                     WHERE id = '$id' ";
            $result3 = mysqli_query($connect,$sql3);
            $type = "update";
            $data = 'homead_goods';
            $time = date("Y-m-d H:i:s",time());
            $description = "修改首页轮播图的信息。"."轮播图编号："."$id";
            $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
            $result3_m = mysqli_query($connect,$sql3_m);
            echo (callback("success","操作成功"));
        }
    }



    //功能：抓取全部‘首页特推商品’活动，返回数据表里的全部信息
    //入参：$session：管理员的session
    //出参：success: 操作成功；      false: session已过期
    public function fetchactivities_goods($session){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql_n = "select * from `homepage_goods` ";
        $result_n = mysqli_query($connect,$sql_n);
        $number = mysqli_num_rows($result_n);
        $array = array();
        $i = 1;
        while ($number>=$i){
            $data = mysqli_fetch_assoc($result_n);
            array_push($array,$data);
            $i++;
        }
        echo (callback("success",$array));
    }





    //功能：修改首页特推商品的信息，并将操作写入log（不用具体）
    //入参：$session：管理员的session；
    //      $id：首页轮播图的id；
    //      $form: 一个键值对形式的数组，键分别为：  `id`, `name`, `unitprice`, `url`, `status`
    //      $adminname：管理员的name
    //出参：success: 操作成功；      false:无此商品  session已过期
    public function modifydetail_goods($session,$id,$form,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql = "select * from `homepage_goods` WHERE id = '$id' ";
        $result = mysqli_query($connect,$sql);
        if (mysqli_num_rows($result) == 0){
            exit(callback("false","无此商品"));
        }
        $form = json_decode($form);
        //传回的值
        $name = $form -> name;
        $unitprice = $form -> unitprice;
        $url = $form -> url;
        $status = $form -> status;
        //数据库查到的值
        $sql1 = "select name,unitprice,url,status from `homepage_goods` WHERE id = '$id' ";
        $result1 = mysqli_query($connect,$sql1);
        $array1 = mysqli_fetch_row($result1);
        $old_name = $array1[0];
        $old_unitprice = $array1[1];
        $old_url = $array1[2];
        $old_status = $array1[3];
        if ($name == $old_name
            && $unitprice == $old_unitprice
            && $url == $old_url
            && $status == $old_status
        ){
            exit(callback("success","操作成功"));
        }else{
            $sql3 = "update `homepage_goods` 
                     set 
                     name = '$name',unitprice = '$unitprice',url = '$url',status = '$status'
                     WHERE id = '$id' ";
            $result3 = mysqli_query($connect,$sql3);
            $type = "update";
            $data = 'homepage_goods';
            $time = date("Y-m-d H:i:s",time());
            $description = "修改首页特推商品的信息。"."首页特推商品编号："."$id";
            $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
            $result3_m = mysqli_query($connect,$sql3_m);
            echo (callback("success","操作成功"));
        }
    }






}