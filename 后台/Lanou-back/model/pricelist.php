<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/2/25
 * Time: 22:09
 */
require_once 'base.php';
require_once '../controller/callback.php';
require_once 'session.php';
class pricelist{

    //功能：注册新价目表商品。并记录此操作。
    //入参：$session：管理员的session；
    //      $form: 数组有2项，是键值对的形式。 [name,goods]
    //      $adminname：管理员的name
    //出参：success: 操作成功；      false: session已过期
    public function newpricelist($session,$form,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $form = json_decode($form);
        $name = $form -> name;
        $new_goods = $form -> goods;
        $sql1 = "select id,goods from `pricelist` WHERE name = '$name' ";
        $result1 = mysqli_query($connect,$sql1);
        if (mysqli_num_rows($result1) == 0){
            //新增一级分类
            $sql = "select * from `pricelist` ";
            $result = mysqli_query($connect,$sql);
            $id = mysqli_num_rows($result) + 1;
            //将数组json编码成字符串,JSON_UNESCAPED_UNICODE是为了防止出现乱码
            $new_goods = json_encode($new_goods,JSON_UNESCAPED_UNICODE);
            $sql2 = "insert into pricelist(name,goods) VALUES ('$name','$new_goods')";
            $result2 = mysqli_query($connect,$sql2);
            $description = "注册新价目表一级分类商品。"."一级分类名："."$name"."，一级分类id："."$id";
        }else{
            $date = mysqli_fetch_row($result1);
            $id = $date[0];
            $goods = $date[1];
            $goods = json_decode($goods);
            array_push($goods,$new_goods);
            //将数组json编码成字符串,JSON_UNESCAPED_UNICODE是为了防止出现乱码
            $goods = json_encode($goods,JSON_UNESCAPED_UNICODE);
            $sql2 = "update `pricelist` set goods = '$goods' WHERE name = '$name' ";
            $result2 = mysqli_query($connect,$sql2);
            $description = "注册新价目表二级分类商品。"."所处一级分类名："."$name"."，一级分类id："."$id";
        }

        //将log表中的time由时间戳转换成Y-m-d H:i:s格式
//        $logid = 1;
//        while ($logid < 105){
//            $sql5 = "select time from `log` WHERE logid = '$logid' ";
//            $result5 = mysqli_query($connect,$sql5);
//            $time = mysqli_fetch_row($result5);
//            $time = $time[0];
//            $time = date("Y-m-d H:i:s",$time);
//            $sql6 = "update `log` set time = '$time' WHERE logid = '$logid' ";
//            $result6 = mysqli_query($connect,$sql6);
//            $logid++;
//        }

        $type = "add";
        $data = 'pricelist';
        $time = date("Y-m-d H:i:s",time());
        $sql4_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
        $result4_m = mysqli_query($connect,$sql4_m);
        echo (callback("success","操作成功"));
    }



    //功能：更改价目表商品一级分类状态（0-1）或删除此一级分类或删除此一级分类的某二级分类，写入log
    //入参：$session：管理员的session；  $id：价目表商品一级分类的id；  $type：操作类型(可选：changestatus/delete/二级分类索引值index（如0 1 2）)；  $adminname：管理员的name
    //出参：success: 操作成功；      false:无此商品  session已过期
    public function modifyinfo($session,$id,$type,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql = "select * from `pricelist` WHERE id = '$id' ";
        $result = mysqli_query($connect,$sql);
        if (mysqli_num_rows($result) == 0){
            exit(callback("false","无此商品"));
        }
        $sql2 = "select `name`,`status` from `pricelist` WHERE id = '$id' ";
        $result2 = mysqli_query($connect,$sql2);
        $array = mysqli_fetch_row($result2);
        $name = $array[0];
        $status = $array[1];
        switch ($type){
            //更改价目表商品状态（0-1）
            case 'changestatus':
                $type = "update";
                if ($status == 1){
                    $sql3 = "update `pricelist` set status = 0 WHERE id = '$id' ";
                    $description = "改变价目表商品status为0。"."所处一级分类名："."$name"."，一级分类id："."$id";
                }else{
                    $sql3 = "update `pricelist` set status = 1 WHERE id = '$id' ";
                    $description = "改变价目表商品status为1。"."所处一级分类名："."$name"."，一级分类id："."$id";
                }
                $result3 = mysqli_query($connect,$sql3);
                break;
            //删除此价目表商品一级分类
            case 'delete':
                $sql5 = "delete from `pricelist` WHERE id = '$id' ";
                $result5 = mysqli_query($connect,$sql5);
                $description = "删除此价目表商品一级分类。"."一级分类名："."$name"."，一级分类id："."$id";
                break;
            //删除此一级分类的某二级分类
            default:
                $sql5 = "select goods from `pricelist` WHERE id = '$id' ";
                $result5 = mysqli_query($connect,$sql5);
                $goods = mysqli_fetch_row($result5);
                $goods = $goods[0];
                $goods = json_decode($goods);
                array_splice($goods,$type,1);
                //将数组json编码成字符串,JSON_UNESCAPED_UNICODE是为了防止出现乱码
                $goods = json_encode($goods,JSON_UNESCAPED_UNICODE);
                $sql6 = "update `pricelist` set goods = '$goods' WHERE id = '$id' ";
                $result6 = mysqli_query($connect,$sql6);
                $description = "删除此价目表商品二级分类。"."所处一级分类名："."$name"."，一级分类id："."$id";
                $type = "delete";
                break;
        }
        $data = 'pricelist';
        $time = date("Y-m-d H:i:s",time());
        $sql5_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
        $result5_m = mysqli_query($connect,$sql5_m);
        echo (callback("success","操作成功"));
    }



    //功能：修改价目表商品的信息，并将操作写入log
    //入参：$session：管理员的session；
    //      $id：商品一级分类的id；
    //      $form: 一个键值对形式的数组，键分别为： $form:[id,name,goods[0-name,1-url,2-unitprice],status]
    //      $adminname：管理员的name
    //出参：success: 操作成功；      false:无此商品 session已过期
    public function modifydetail($session,$id,$form,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql = "select * from `pricelist` WHERE id = '$id' ";
        $result = mysqli_query($connect,$sql);
        if (mysqli_num_rows($result) == 0){
            exit(callback("false","无此商品"));
        }
        $form = json_decode($form);
        //传回的值
        $name = $form -> name;
        $goods = $form -> goods;
        $status = $form -> status;
        $sql5 = "select goods from `pricelist` WHERE id = '$id' ";
        $result5 = mysqli_query($connect,$sql5);
        $goods = mysqli_fetch_row($result5);
        $goods = $goods[0];
        $goods = json_decode($goods);
        array_splice($goods,$index,1,$new_goods);
        $goods = json_encode($goods,JSON_UNESCAPED_UNICODE);
        $sql6 = "update `pricelist` set name = '$name',status = '$status',goods = '$goods' WHERE id = '$id' ";
        $result6 = mysqli_query($connect,$sql6);
        $description = "修改价目表商品的信息。"."所处一级分类名："."$name"."，一级分类id："."$id";
        $type = "update";
        $data = 'pricelist';
        $time = date("Y-m-d H:i:s",time());
        $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
        $result3_m = mysqli_query($connect,$sql3_m);
        echo (callback("success","操作成功"));
    }



    //功能：抓取除status的所有价目表信息（客户端），只抓status = 1的
    //入参：无
    //出参：success: $array数组
    public function fetchpricelist(){
        global $connect;
        $sql1 = "select id,name,goods from `pricelist` WHERE status = 1 ";
        $result1 = mysqli_query($connect,$sql1);
        $number = mysqli_num_rows($result1);
        $result = array();
        while ($number > 0){
            $array = mysqli_fetch_assoc($result1);
            array_push($result,$array);
            $number--;
        }
        echo (callback("success",$result));
    }

    //功能：抓取所有价目表信息（管理员端），抓所有status
    //入参：无
    //出参：success: $array数组
    public function fetchpricelist_m(){
        global $connect;
        $sql1 = "select id,name,goods,status from `pricelist` ";
        $result1 = mysqli_query($connect,$sql1);
        $number = mysqli_num_rows($result1);
        $result = array();
        while ($number > 0){
            $array = mysqli_fetch_assoc($result1);
            array_push($result,$array);
            $number--;
        }
        echo (callback("success",$result));
    }

}