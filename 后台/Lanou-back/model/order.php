<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/1/26
 * Time: 下午4:54
 */
require_once 'base.php';
require_once 'session.php';
require_once '../controller/callback.php';

class order{

    //功能：下订单
    //入参：用户$session，地址索引$index，选择的日期0-2$appoint_date，预约时间0-5$appoint_time，重量$evaluate_weight，备注$remarks，url1：$url1，url2：$url2，url3：$url3
    //出参： success:下单成功  false:session已过期
    public function neworder($session,$index,$appoint_date,$appoint_time,$evaluate_weight,$remarks,$url1,$url2,$url3){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode -> checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $nom_time = time();
        //将对应的user地址数组取出
        $sql1 = "select address from `user` WHERE openid = '$openid' ";
        $result1 = mysqli_query($connect,$sql1);
        $address = mysqli_fetch_row($result1);
        $address = $address[0];
        $address = json_decode($address);
        $address = $address[$index];
        //取$address数组中的省市区
        $address1 = "$address[0]".","."$address[1]".","."$address[2]";
        //取$address数组中的小区
        $address2 = $address[4];
        //取$address数组中的楼号
        $address3 = $address[6];
        //取$address数组中的详细地址
        $address4 = $address[8];
        //取$address数组中的城市
        $address5 = $address[1];
        //取$address数组中的联系人
        $contact_name = $address[9];
        //取$address数组中的手机号
        $phonenumber = $address[10];
        switch($evaluate_weight){
            case 1:
                $evaluate_weight = 1;
                break;
            case 2:
                $evaluate_weight = 2;
                break;
            case 3:
                $evaluate_weight = 3;
                break;
            case 4:
                $evaluate_weight = 5;
                break;
            case 5:
                $evaluate_weight = 10;
                break;
            case 6:
                $evaluate_weight = 20;
                break;
            case 7:
                $evaluate_weight = 50;
                break;
            case 8:
                $evaluate_weight = '50+';
                break;
        }
        if($appoint_date == 1 or $appoint_date == 2){
            $appoint_date = time();
        }else{
            $appoint_date = time() + 86400;
        }
        if($url1 === '0'){
            echo ($url1);
            $url1 = "";
        }
        if($url2 === '0'){
            $url2 = "";
        }
        if($url3 === '0'){
            $url3 = "";
        };
        if($remarks === '0'){
            $remarks = "";
        }
        $id = 7;
        $sql = "select phonenumber,name from `collector` WHERE collectorid = '$id' ";
        $result = mysqli_query($connect,$sql);
        $array = mysqli_fetch_row($result);
        $collector_name = $array[1];
        $collector_phonenumber = $array[0];
        $sql2 = "insert into
                 `order`(`openid`,`appoint_date`,`appoint_time`,`contact_name`,`address1`,`address2`,`address3`,`address4`,`address5`,`phonenumber`,`evaluate_weight`,`collector_name`,`collector_phonenumber`,`remarks`,`url1`,`url2`,`url3`,`order_time`) 
                 VALUES
                 ('$openid','$appoint_date','$appoint_time','$contact_name','$address1','$address2','$address3','$address4','$address5','$phonenumber','$evaluate_weight','$collector_name','$collector_phonenumber','$remarks','$url1','$url2','$url3','$nom_time') ";
        $result2 = mysqli_query($connect,$sql2);
//        echo (callback("success","下单成功"));
    }



    //功能：评价订单
    //入参：$session: 用户session，$orderid 订单id ,$rate_star 评价星级,$rate_text 评价内容
    //出参： success:评价成功   false:session已过期
    public function evaluateorder($session,$orderid,$rate_star,$rate_text){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode -> checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $rate_time = time();
        $sql1 = "update `order` set rate_star = '$rate_star',rate_text = '$rate_text',rate_time = '$rate_time' WHERE orderid = '$orderid' ";
        $result1 = mysqli_query($connect,$sql1);
        echo (callback("success","评价成功"));
    }


    //功能：取消订单
    //入参：用户$session，订单openid
    //出参： success:取消订单成功
    public function cancelorder($session,$orderid){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql1 = "update `order` set status = 0 WHERE openid = '$openid' and orderid = '$orderid' ";
        $result1 = mysqli_query($connect,$sql1);
        echo callback("success","取消订单成功");
    }


    //功能：投诉订单
    //入参：$session: 用户session，$orderid 订单id ,$complaint_text 投诉内容
    //出参： success:投诉成功   false:session已过期
    public function complaintorder($session,$orderid,$complaint_text){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode -> checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $complaint_time = date("Y-m-d H:i:s",time());
        $sql1 = "update `order` set complaint_text = '$complaint_text',complaint_time = '$complaint_time' WHERE orderid = '$orderid' ";
        $result1 = mysqli_query($connect,$sql1);
        echo (callback("success","投诉成功"));
    }


    //功能：查询用户正在进行中的订单信息并返回信息
    //入参：用户$session
    //出参： success: 返回msg为一个数组，0“小区楼号详细地址”，1“联系人+手机号”，2预约日期“年-月-日”，
    //       3预约时间“xx:xx-xx:xx”,4预计重量（不带KG），5项为回收员姓名，6项为回收员完整手机号，7项为备注，8项是图片url1
    //       ，9项是图片url2，10项是图片url3，11项是订单id，12项是下单时间;
    //       false:无历史订单    false:session已过期
    public function getordering($session){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $result1=mysqli_query($connect,"select `address2`,`phonenumber`,`evaluate_weight`,`collector_name`,`collector_phonenumber`,`remarks`,`url1`,`url2`,`url3`,`orderid`,`order_time` from `order` WHERE openid = '$openid' and status = 2 or status = 1 ");
        $array=mysqli_fetch_row($result1);
        //取出预约日期“年-月-日
        $result2 = mysqli_query($connect,"select `appoint_date` from `order` WHERE openid = '$openid' and status = 2 or status = 1 ");
        $appoint_date=mysqli_fetch_row($result2);
        $appoint_date = $appoint_date[0];
        $appoint_date = date("Y-m-d",$appoint_date);
        //取出预约时间”xx:xx-xx:xx
        $result3 = mysqli_query($connect,"select `appoint_time` from `order` WHERE openid = '$openid' and status = 2 or status = 1 ");
        $appoint_time = mysqli_fetch_row($result3);
        $appoint_time = $appoint_time[0];
//        $appoint_time = date("H:i",$appoint_time);  屏蔽
        //将 预约日期 预约时间 插入到数组array中
        array_splice($array, 2, 0, $appoint_date);
        array_splice($array, 3, 0, $appoint_time);
        $phonenumber = $array[1];
        //取出联系人名字
        $resulta = mysqli_query($connect,"select contact_name from `order` WHERE openid = '$openid' and status = 2 or status = 1 ");
        $contact_name = mysqli_fetch_row($resulta);
        $contact_name = $contact_name[0];
        $array[1] = "$contact_name"." "."$phonenumber";
        $result4 = mysqli_query($connect,"select address3,address4,address5 from `order` WHERE openid = '$openid' and status = 2 or status = 1 ");
        $address =  mysqli_fetch_row($result4);
        foreach ($address as $item){
            array_push($array,$item);
        }
        echo callback("success",$array);
    }



    //功能：查询用户历史订单
    //入参：用户$session
    //出参：success: 包含所有历史订单的数组  ;    false:无历史订单    false:session已过期
    public function queryorders($session,$demand){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql1 = "SELECT * FROM `order` WHERE openid = '$openid' and $demand ";
        $result1 = mysqli_query($connect,$sql1);
        if (mysqli_num_rows($result1) > 0){
            //存在历史订单
            $sql2 = "SELECT orderid,status,address2,address3,address4,address5,url1,order_time,order_money,rate_star FROM `order` ORDER BY order_time DESC"; //顺序改动
            $result2 = mysqli_query($connect,$sql2);
            $result = array();
            while($data = mysqli_fetch_assoc($result2)){
                $data['order_time'] = date("Y-m-d H:i",$data['order_time']);
                array_push($result,$data);
            }
            echo callback("success",$result);
        }else{
            //无历史订单
            echo callback("false","无历史订单");
        }
    }



    //功能：按照条件抓取对应的订单列表;
    //入参：用户$session，$listquery（查询条件json数组）
    //出参：success:数组[items,total]     ；   false:session已过期
    public function fetchorders($session, $listquery)
    {
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql1 = "select * from `order`";
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
            } else if ($querytitle == 'name') {
                $where1 = ' and contact_name = ' . "'$queryvalue'";
            } else if ($querytitle == 'neighborhood') {
                $where1 = ' and  address2 = ' . "'$queryvalue'";
            } else if($querytitle == 'collectorid'){
                $where1 = ' and  collectorid = ' . "'$queryvalue'".' and  status = 3 ';
            }else {
                $where1 = '';
            }

            if ($querycity) {
                $where2 = ' and address5 = ' . "'$querycity'";
            }
            $select = 'orderid,userid,collectorid,status,appoint_date,appoint_time,contact_name,
            address1,address2,address3,address4,address5,phonenumber,evaluate_weight,evaluate_type,
            collector_name,collector_phonenumber,remarks,order_time,waste_detail,order_money,take_time,
            send_time,rate_star,rate_text,rate_time,complaint_text,complaint_time';
            $sql2 = "SELECT $select FROM `order` 
                      WHERE 1=1 $where1 $where2 ORDER BY orderid ASC"; //顺序改动
            $result2 = mysqli_query($connect, $sql2);
            $items = array();
            while ($data = mysqli_fetch_assoc($result2)) {
                if($data['appoint_date']){
                    $data['appoint_date'] = date("Y-m-d", $data['appoint_date']);
                }
                if($data['take_time']){
                    $data['take_time'] = date("Y-m-d H:i", $data['take_time']);
                }
                if($data['send_time']){
                    $data['send_time'] = date("Y-m-d H:i", $data['send_time']);
                }
                if($data['rate_time']){
                    $data['rate_time'] = date("Y-m-d H:i", $data['rate_time']);
                }
                if($data['complaint_time']){
                    $data['complaint_time'] = date("Y-m-d H:i", $data['complaint_time']);
                }
                $data['order_time'] = date("Y-m-d H:i", $data['order_time']);
                array_push($items, $data);
            }
            $result['total'] = count($items);
            $items = array_chunk($items, $querylimit);
            $result['items'] = $items[$querypage - 1];
            echo callback("success", $result);
        } else {
            //无用户
            echo callback("false", "未查询到订单");
        }
    }

    //功能：抓取该历史订单的订单详情
    //入参：$session：用户session， $orderid：订单id
    //出参：success：数组array[orderid,userid,collectorid,status,appoint_date,appoint_time,contact_name,
    //address1,address2,address3,address4,address5,phonenumber,evaluate_weight,evaluate_type,
    //collector_name,collector_phonenumber,remarks,order_time,waste_detail,order_money,take_time,
    //send_time,rate_star,rate_text,rate_time,complaint_text,complaint_time]
    //      false: 无此历史订单 session已过期
    public function getorderinfo($session,$orderid){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql1 = "select * from `order` WHERE orderid = '$orderid' ";
        $result1 = mysqli_query($connect,$sql1);
        if (mysqli_num_rows($result1) == 0){
            exit(callback("false","无此历史订单"));
        }
        $sql2 = "select orderid,userid,collectorid,status,appoint_date,appoint_time,contact_name,
            address1,address2,address3,address4,address5,phonenumber,evaluate_weight,evaluate_type,
            collector_name,collector_phonenumber,remarks,order_time,waste_detail,order_money,take_time,
            send_time,rate_star,rate_text,rate_time,complaint_text,complaint_time from `order` WHERE orderid = '$orderid'";
        $result2 = mysqli_query($connect,$sql2);
        $array = mysqli_fetch_assoc($result2);
        $array['appoint_date'] = date("Y-m-d",time());
        $array['order_time'] = date("Y-m-d",time());
        echo (callback("success",$array));
    }

    /*-------------------------以下为管理员面板功能------------------------------*/


    //功能：改派回收员或关闭此订单或删除此订单
    //入参：$session:用户session
    //      $orderid:订单id
    //      $type : 可选：changecollector/changestatus/delete
    //      $adminname：操作者的username
    //出参：success: 操作成功;    false: 无此订单  无空闲回收员  session已过期
    public function modifyinfo($session,$orderid,$type,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql1 = "SELECT * FROM `order` WHERE orderid = '$orderid' ";
        $result1 = mysqli_query($connect,$sql1);
        if(mysqli_num_rows($result1) == 0){
            exit(callback("false","无此订单"));
        }
        //取出该订单用户的联系人姓名和手机号和地址
        $sql2 = "select collectorid,contact_name,address2,address5,phonenumber,collector_name,collector_phonenumber from `order` WHERE orderid = '$orderid' ";
        $result2 = mysqli_query($connect,$sql2);
        $array1 = mysqli_fetch_row($result2);
        $old_collectorid = $array1[0];
        $contact_name = $array1[1];
        $address2 = $array1[2];
        $address5 = $array1[3];
        $phonenumber = $array1[4];
        $collector_name = $array1[5];
        $collector_phonenumber = $array1[6];
        $time = date("Y-m-d H:i:s",time());
        switch ($type){
//          改派回收员
            case 'changecollector':
                $sql3 = "select collectorid,phonenumber,name from `collector` WHERE status = 2 AND address1 = '$address5' AND address2 = '$address2' ";
                $result3 = mysqli_query($connect,$sql3);
                if (mysqli_num_rows($result3) == 0){
                    exit(callback("false","无空闲回收员"));
                }
                $array2 = mysqli_fetch_row($result3);
                $collectorid = $array2[0];
                $new_collector_phonenumber = $array2[1];
                $new_collector_name = $array2[2];
                $sql4 = "update `order` 
                         set 
                         status = 3,collectorid = '$collectorid',collector_name = '$new_collector_name',collector_phonenumber = '$new_collector_phonenumber' 
                         WHERE orderid = '$orderid' ";
                $result4 = mysqli_query($connect,$sql4);
                $sql4_u = "update `collector` set status = 1 WHERE collectorid = '$old_collectorid'";
                $result4_u = mysqli_query($connect,$sql4_u);
                $type = "update";
                $data = 'order';
                $description = "向用户改派回收员。"."联系人姓名："."$contact_name"."，联系人手机号："."$phonenumber"."。新的回收员姓名："."$collector_name"."，回收员电话号："."$collector_phonenumber";
                $sql4_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                $result4_m = mysqli_query($connect,$sql4_m);
                echo (callback("success","操作成功"));
                break;
//          关闭此订单
            case 'changestatus':
                $sql5 = "update `order` set status = 0 WHERE orderid = '$orderid' ";
                $result5 = mysqli_query($connect,$sql5);
                $type = "update";
                $data = 'order';
                $description = "关闭该订单。"."联系人姓名："."$contact_name"."，联系人手机号："."$phonenumber"."。回收员姓名："."$collector_name"."，回收员电话号："."$collector_phonenumber";
                $sql5_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                $result5_m = mysqli_query($connect,$sql5_m);
                echo (callback("success","操作成功"));
                break;
//          删除此订单
            case 'delete':
                $sql6 = "delete from `order` WHERE orderid = '$orderid' ";
                $result6 = mysqli_query($connect,$sql6);
                $type = "delete";
                $data = 'order';
                $description = "删除该订单。"."联系人姓名："."$contact_name"."，联系人手机号："."$phonenumber"."。回收员姓名："."$collector_name"."，回收员电话号："."$collector_phonenumber";
                $sql6_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                $result6_m = mysqli_query($connect,$sql6_m);
                echo (callback("success","操作成功"));
                break;
        }
    }



    //功能：修改订单信息，并将操作写入log
    //入参：$session:用户session
    //      $orderid:订单id
    //      $form : 形式与SELECT orderid,userid,collectorid,status,appoint_date,appoint_time,contact_name, address1,address2,address3,address4,address5,phonenumber,evaluate_weight,evaluate_type,collector_name,collector_phonenumber,remarks,order_time,waste_detail,order_money,take_time,send_time,rate_star,rate_text,rate_time,complaint_text,complaint_time FROM order 的mysqli_fetch_assoc结果相同，是一个键值对形式的数组
    //      $adminname：操作者的username
    //出参：success: 操作成功;    false: 无此订单  session已过期
    public function modifydetail($session,$orderid,$form,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql1 = "select * from `order` WHERE orderid = '$orderid' ";
        $result1 = mysqli_query($connect,$sql1);
        if ( mysqli_num_rows($result1) == 0){
            exit(callback("false","无此订单"));
        }
        $form = json_decode($form);

        $userid = $form -> userid;
        $collectorid = $form -> collectorid;
        $status = $form -> status;
        $appoint_date = $form -> appoint_date;
        $appoint_time = $form -> appoint_time;
        $contact_name = $form -> contact_name;
        $address1 = $form -> address1;
        $address2 = $form -> address2;
        $address3 = $form -> address3;
        $address4 = $form -> address4;
        $address5 = $form -> address5;
        $phonenumber = $form -> phonenumber;
        $evaluate_weight = $form -> evaluate_weight;
        $evaluate_type = $form -> evaluate_type;
        $collector_name = $form -> collector_name;
        $collector_phonenumber = $form -> collector_phonenumber;
        $remarks = $form -> remarks;
        $order_time = $form -> order_time;
        $order_money = $form -> order_money;
        $take_time = $form -> take_time;
        $send_time = $form -> send_time;
        $rate_star = $form -> rate_star;
        $rate_text = $form -> rate_text;
        $rate_time = $form -> rate_time;
        $complaint_text = $form -> complaint_text;
        $complaint_time = $form -> complaint_time;

        $sql3 = "update `order` 
                 set
                 userid = '$userid',collectorid = '$collectorid',status = '$status',appoint_date = '$appoint_date',appoint_time = '$appoint_time',contact_name = '$contact_name', address1 = '$address1',address2 = '$address2',address3 = '$address3',address4 = '$address4',address5 = '$address5',phonenumber = '$phonenumber',evaluate_weight = '$evaluate_weight',evaluate_type = '$evaluate_type',collector_name = '$collector_name',collector_phonenumber = '$collector_phonenumber',remarks = '$remarks',order_time = '$order_time',order_money = '$order_money',take_time = '$take_time',send_time = '$send_time',rate_star = '$rate_star',rate_text = '$rate_text',rate_time = '$rate_time',complaint_text = '$complaint_text',complaint_time = '$complaint_time'
                 WHERE orderid = '$orderid' ";
        $result3 = mysqli_query($connect,$sql3);
        $type = "update";
        $data = 'order';
        $time = date("Y-m-d H:i:s",time());
        $description = "修改订单信息。"."订单id："."$orderid";
        $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
        $result3_m = mysqli_query($connect,$sql3_m);
        echo (callback("success","操作成功"));
    }



    //功能：确认订单已送货(状态设为4)
    //入参：$session:用户session
    //      $orderid:订单id
    //      $adminname：操作者的username
    //出参：success: 操作成功;    false: 无此订单  session已过期
    public function checkorder($session,$orderid,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql1 = "select * from `order` WHERE orderid = '$orderid' ";
        $result1 = mysqli_query($connect,$sql1);
        if ( mysqli_num_rows($result1) == 0){
            exit(callback("false","无此订单"));
        }
        $sql1 = "update `order` set status = 4 WHERE orderid = '$orderid' ";
        $result1 = mysqli_query($connect,$sql1);
        $type = "update";
        $data = 'order';
        $time = date("Y-m-d H:i:s",time());
        $description = "确认订单已送货。"."订单id："."$orderid";
        $sql1_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
        $result1_m = mysqli_query($connect,$sql1_m);
        echo (callback("success","操作成功"));
    }

    /*-------------------------以下为管理员面板功能------------------------------*/
    //功能：抓取某回收员的所有订单
    //入参：$session:用户session
    //出参：success:嵌套数组array[status,orderid,appoint_date:预约日期,appoint_time:预约时间段,address2:小区,address3:楼号,address4:详细地址,address5:城市,url1,collector_status:参数（预约中 待送货 已完成）]   ,  false:session已过期
    public function fetchcollectororder($session){
        global $connect;
//        $sessioncode = new session();
//        $collectorid= false;
//        $collectorid = $sessioncode->checksession($session);
//        if(!$collectorid){
//            exit(callback("false","session已过期"));
//        }
        $collectorid = $session;
        $sql1 = "select orderid,status,appoint_date,appoint_time,address2,address3,address4,address5,url1 from `from` WHERE collectorid = '$collectorid' ";
        $result1 = mysqli_query($connect,$sql1);
        $array = array();
        $sql2 = "select status from `collector` WHERE collectorid = '$collectorid' ";
        $result2 = mysqli_query($connect,$sql2);
        $collector_status = mysqli_fetch_row($result2);
        $collector_status = $collector_status[0];
        while($data = mysqli_fetch_assoc($result1)){
            array_push($data,$collector_status);
            array_push($array,$data);
        }
        echo callback("success",$array);
    }
}