<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/2/7
 * Time: 上午1:02
 */
require_once 'base.php';
require_once '../controller/callback.php';
require_once 'session.php';
class area{

    //功能：注册新小区。并记录此操作。
    //入参：$session：管理员的session；
    //      $form: 数组有4项，是键值对的形式。 {district:’’,city:’’,name:’’,building_info:[‘’]}
    //      $adminname：管理员的name
    //出参：success: 操作成功；      false: session已过期
    public function newarea($session,$form,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $form = json_decode($form);
        $district = $form -> district;
        $sql1 = "select district_id from `district` WHERE name = '$district' ";
        $result1 = mysqli_query($connect,$sql1);
        $district_id = mysqli_fetch_row($result1);
        $district_id = $district_id[0];
        $city = $form -> city;
        $name = $form -> name;
        $building_info = $form -> building_info;
        //将数组json编码成字符串,JSON_UNESCAPED_UNICODE是为了防止出现乱码
        $building_info = json_encode($building_info,JSON_UNESCAPED_UNICODE);
        $add_time = date("Y-m-d H:i:s",time());
        $sql2 = "insert into area(name,district_id,city,building_info,add_time) VALUES ('$name','$district_id','$city','$building_info','$add_time')";
        $result2 = mysqli_query($connect,$sql2);
        $sql3 = "select neighborhoodid from `area` WHERE add_time = '$add_time' ";
        $result3 = mysqli_query($connect,$sql3);
        $neighborhoodid = mysqli_fetch_row($result3);
        $neighborhoodid = $neighborhoodid[0];
        $type = "add";
        $data = 'area';
        $time = date("Y-m-d H:i:s",time());
        $description = "注册新小区。"."$city"."$name"."，小区编号"."$neighborhoodid";
        $sql4_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
        $result4_m = mysqli_query($connect,$sql4_m);
        echo (callback("success","操作成功"));
    }

    //功能：通过省市区抓取已开通服务小区的名称数组
    //入参：用户$province,$city,$district
    //出参：success: 小区名称数组 ; false: 暂无开通小区
    public function fetchneighborhood($province,$city,$district){
        global $connect;
        if ($province == $city){
            $city_name = "$city".","."$district";
        }else{
            $city_name = "$province".","."$city".","."$district";
        }
        $result1 = mysqli_query($connect,"select district_id from `district` WHERE name = '$city_name' ");
        $district_id=mysqli_fetch_row($result1);
        $district_id=$district_id[0];
        $result2 = mysqli_query($connect,"select name from `area` WHERE district_id = '$district_id' ");
        function fetch2array($sql_result){
            $result = array();
            while($data = mysqli_fetch_row($sql_result)){
                $temp = $data[0];
                array_push($result,$temp);
            }
            return $result;
        };
        $result2 = fetch2array($result2);
        if($result2){
            echo callback('success',$result2);
        }else{
            echo callback('false','暂无开通小区');
        }
    }

    //功能：通过小区名称抓取小区楼号情况数组
    //入参：用户$neighborhood
    //出参：success: 楼号情况数组 ; false: 暂无楼号信息
    public function fetchbuildingnumber($neighborhood){
        global $connect;
        $result1 = mysqli_query($connect,"select building_info from `area` WHERE name = '$neighborhood'");
        $building_info = mysqli_fetch_row($result1);
        $building_info = json_decode($building_info[0]);//building_info是一个json数组，要先转换为数组才能传入callback
        if($result1){
            echo callback('success',$building_info);
        }else{
            echo callback('false','暂无楼号信息');
        }
    }

    //功能：按照条件抓取对应的小区列表;
    //入参：用户$session，$listquery（查询条件json数组）
    //出参：success:数组[items,total]     ；   false:session已过期
    public function fetcharea($session, $listquery)
    {
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql1 = "select * from `collector`";
        $result1 = mysqli_query($connect, $sql1);


        $listquery = json_decode($listquery);
        $querypage = $listquery->page;
        $querylimit = $listquery->limit;
        $querycity = $listquery->city;
        $querytitle = $listquery->title;
        $queryvalue = $listquery->value;

        if (mysqli_num_rows($result1) > 0) {
            //存在用户
            if ($querytitle == 'neighborhoodid') {
                $where1 = ' and '. $querytitle . ' = ' . $queryvalue;
            } else if ($querytitle == 'city') {
                $where1 = ' and '. $querytitle . ' = ' . "'$queryvalue'";
            } else if ($querytitle == 'name') {
                $where1 = ' and '. $querytitle . ' = ' . "'$queryvalue'";
            }else{
                $where1 = '';
            }

            if ($querycity) {
                $where2 = ' and address1 = ' . "'$querycity'";
            }
            $select = 'neighborhoodid,name,district_id,city,building_info,add_time,status';
            $sql2 = "SELECT $select FROM `area` 
                      WHERE 1=1 $where1 $where2 ORDER BY neighborhoodid ASC"; //顺序改动
            $result2 = mysqli_query($connect, $sql2);
            $items = array();
            while ($data = mysqli_fetch_assoc($result2)) {
                $sql3 = "SELECT name FROM `district` 
                      WHERE district_id = ".$data['district_id']." ";
                $result3 = mysqli_query($connect, $sql3);
                $temp = mysqli_fetch_row($result3)[0];
                $data['district'] = $temp;
                $data['registertime'] = date("Y-m-d H:i", $data['registertime']);
                array_push($items, $data);
            }
            $result['total'] = count($items);
            $items = array_chunk($items, $querylimit);
            $result['items'] = $items[$querypage - 1];
            echo callback("success", $result);
        } else {
            //无用户
            echo callback("false", "未查询到回收员");
        }
    }



    //功能：更改小区状态（0-1）或删除此小区，写入log
    //入参：$session：管理员的session；  $neighborhoodid：社区的id；  $type：操作类型(可选：changestatus/delete)；  $adminname：管理员的name
    //出参：success: 操作成功；      false:无此小区  session已过期
    /**
     * @param $session
     * @param $neighborhoodid
     * @param $type
     * @param $adminname
     */
    public function modifyinfo($session, $neighborhoodid, $type, $adminname){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql = "select * from `area` WHERE neighborhoodid = '$neighborhoodid' ";
        $result = mysqli_query($connect,$sql);
        if (mysqli_num_rows($result) == 0){
            exit(callback("false","无此小区"));
        }
        $data = 'area';
        $time = date("Y-m-d H:i:s",time());
        $sql2 = "select `neighborhoodid`,`name`,`city`,`status` from `area` WHERE neighborhoodid = '$neighborhoodid' ";
        $result2 = mysqli_query($connect,$sql2);
        $array = mysqli_fetch_row($result2);
        $neighborhoodid = $array[0];
        $name = $array[1];
        $city = $array[2];
        $status = $array[3];
        switch ($type){
            //更改小区状态（0-1）
            case 'changestatus':
                $type = "update";
                $data = 'area';
                if ($status == 1){
                    $sql3 = "update `area` set status = 0 WHERE neighborhoodid = '$neighborhoodid' ";
                    $result3 = mysqli_query($connect,$sql3);
                    $description = "改变小区status为0。"."$city"."$name"."，小区编号"."$neighborhoodid";
                    $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                    $result3_m = mysqli_query($connect,$sql3_m);
                    echo (callback("success","操作成功"));
                }else{
                    $sql4 = "update `area` set status = 1 WHERE neighborhoodid = '$neighborhoodid' ";
                    $result4 = mysqli_query($connect,$sql4);
                    $description = "改变小区status为1。"."$city"."$name"."，小区编号"."$neighborhoodid";
                    $sql4_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                    $result4_m = mysqli_query($connect,$sql4_m);
                    echo (callback("success","操作成功"));
                }
                break;
            //删除此小区
            case 'delete':
                $sql5 = "delete from `area` WHERE neighborhoodid = '$neighborhoodid' ";
                $result5 = mysqli_query($connect,$sql5);
                $description = "删除此小区。"."$city"."$name"."，小区编号"."$neighborhoodid";
                $sql5_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                $result5_m = mysqli_query($connect,$sql5_m);
                echo (callback("success","操作成功"));
                break;
        }
    }



    //功能：修改小区的信息，并将操作写入log
    //入参：$session：管理员的session；
    //      $neighborhoodid：小区的id；
    //      $form: 一个键值对形式的数组，键分别为： neighborhoodid,name,district_id,city,building_info,add_time,status，district
    //      $adminname：管理员的name
    //出参：success: 操作成功；      false:无此小区  session已过期
    public function modifydetail($session,$neighborhoodid,$form,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql = "select * from `area` WHERE neighborhoodid = '$neighborhoodid' ";
        $result = mysqli_query($connect,$sql);
        if (mysqli_num_rows($result) == 0){
            exit(callback("false","无此小区"));
        }
        $form = json_decode($form);
        //传回的值
        $name = $form -> name;
        $city = $form -> city;
        $building_info = $form -> building_info;
        //将数组json编码成字符串,JSON_UNESCAPED_UNICODE是为了防止出现乱码
        $building_info = json_encode($building_info,JSON_UNESCAPED_UNICODE);
        $status = $form -> status;
        $district = $form -> district;
        $sql2 = "select district_id from `district` WHERE name = '$district' ";
        $result2 = mysqli_query($connect,$sql2);
        $district_id = mysqli_fetch_row($result2);
        $district_id = $district_id[0];
        //数据库查到的值
        $sql1 = "select name,district_id,city,building_info,status from `area` WHERE neighborhoodid = '$neighborhoodid' ";
        $result1 = mysqli_query($connect,$sql1);
        $array1 = mysqli_fetch_row($result1);
        $old_name = $array1[0];
        $old_district_id = $array1[1];
        $old_city = $array1[2];
        $old_building_info = $array1[3];
        $old_status = $array1[4];
        if ($name == $old_name
            && $district_id == $old_district_id
            && $city == $old_city
            && $building_info == $old_building_info
            && $status == $old_status
        ){
            exit(callback("success","操作成功"));
        }else{
            $sql3 = "update `area` 
                     set 
                     name = '$name',district_id = '$district_id',city = '$city',building_info = '$building_info',status = '$status' 
                     WHERE neighborhoodid = '$neighborhoodid' ";
            $result3 = mysqli_query($connect,$sql3);
            $type = "update";
            $data = 'area';
            $time = date("Y-m-d H:i:s",time());
            $description = "修改小区的信息。"."原地址$city"."$name"."，原小区编号"."$neighborhoodid";
            $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
            $result3_m = mysqli_query($connect,$sql3_m);
            echo (callback("success","操作成功"));
        }
    }


}