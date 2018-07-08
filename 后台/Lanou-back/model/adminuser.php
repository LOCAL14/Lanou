<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/2/17
 * Time: 上午12:00
 */
require_once 'base.php';
require_once 'session.php';
require_once '../controller/callback.php';
class adminuser{
    //功能：添加新管理员，并将操作写入log（不用具体）
    //入参：$session：管理员的session；
    //      $form ：一个键值对形式的数组，键分别为：username,password,city(city只有role=0或2时才会有值）,role
    //      $adminname：操作者的name
    //出参：success: 操作成功 用户名重复；      false: session已过期
    public function newadminuser($session,$form,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $form = json_decode($form);
        $username = $form -> username;
        $password = $form -> password;
        $city = $form -> city;
        $role = $form -> role;
        $url = 'https://ws4.sinaimg.cn/large/006tKfTcly1foggl6bzcfj305k05kmx6.jpg';
        $sql2 = "select * from `adminuser` WHERE username = '$username' ";
        $result2 = mysqli_query($connect,$sql2);
        if (mysqli_num_rows($result2) > 0){
            exit(callback("success","用户名重复"));
        }
        $sql = "select * from `adminuser` ";
        $result = mysqli_query($connect,$sql);
        $all_id = mysqli_num_rows($result);
        $id = $all_id + 1;
        if ($role == 0 || $role == 2){
            $sql1 = "insert into 
                 `adminuser`(`id`,`username`,`password`,`avatarurl`,`role`,`city`) 
                 VALUES 
                 ('$id','$username','$password','$url','$role','$city') ";
            $result1 = mysqli_query($connect,$sql1);
        }else{
            $sql1 = "insert into 
                 `adminuser`(`id`,`username`,`password`,`avatarurl`,`role`) 
                 VALUES 
                 ('$id','$username','$password','$url','$role') ";
            $result1 = mysqli_query($connect,$sql1);
        }
        $type = "add";
        $data = 'adminuser';
        $time = date("Y-m-d H:i:s",time());
        $description = "添加新管理员。"."管理员编号："."$id"."，管理员姓名："."$username";
        $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
        $result3_m = mysqli_query($connect,$sql3_m);
        echo (callback("success","操作成功"));
    }



    //管理员登录
    public function login($username,$password){
        global $connect;
        $query1 = "select * from adminuser where username = '$username' and password = '$password' and status = 1";
        $result1 = mysqli_query($connect, $query1);
        if (mysqli_num_rows($result1) == 0) {
            exit(callback("false", "用户未注册"));
        }else{
            $sql1 = "SELECT `avatarurl`, `role`, `city` FROM adminuser WHERE username = '$username' ";
            $result1 = mysqli_query($connect,$sql1);
            $result1 = mysqli_fetch_row($result1);
            $avatarurl = $result1[0];
            $role = $result1[1];
            $city = $result1[2];

            $session_key = 0;      //对于管理员来讲 session_key约定为0
            $code = rand(1000000000,9999999999);
            $openid = $username;   //对于管理员来讲 username就是openid
            $setsession = new session();
            $setsession -> newsession($code,$openid,$session_key);

            $array = array($avatarurl,$role,$code,$city);
            echo (callback("success",$array));
        }
    }


    //管理员登出
    public function logout($session){
        $sessioncode = new session();
        $sessioncode->deletesession($session);
        echo (callback("success","操作成功"));
    }


    //抓取该管理员信息
    public function getinfo($session){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $query1 = "select * from adminuser where username = '$openid' and status = 1 ";
        $result1 = mysqli_query($connect, $query1);
        if (mysqli_num_rows($result1) == 0) {
            exit(callback("false", "用户未注册"));
        }
        $sql1 = "SELECT `avatarurl`, `role`, `city` FROM `adminuser` WHERE  username = '$openid' and status = 1 ";
        $result1 = mysqli_query($connect,$sql1);
        $result1 = mysqli_fetch_row($result1);
        $avatarurl = $result1[0];
        $role = $result1[1];
        $city = $result1[2];
        $array = array($avatarurl,$role,$openid,$city);
        echo (callback("success",$array));
    }

    //功能：按照条件抓取对应的管理员列表;
    //入参：用户$session，$listquery（查询条件json数组）
    //出参：success:数组[items,total]     ；   false:session已过期
    public function fetchadminusers($session, $listquery)
    {
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $sql1 = "select * from `adminuser`";
        $result1 = mysqli_query($connect, $sql1);


        $listquery = json_decode($listquery);
        $querypage = $listquery->page;
        $querylimit = $listquery->limit;
        $querycity = $listquery->city;
        $querytitle = $listquery->title;
        $queryvalue = $listquery->value;

        if (mysqli_num_rows($result1) > 0) {
            //存在用户
            if ($querytitle == 'city') {
                $where1 = ' and '. $querytitle . ' = ' . $queryvalue;
            } else if ($querytitle == 'name') {
                $where1 = ' and '. $querytitle . ' = ' . "'$queryvalue'";
            } else if ($querytitle == 'id') {
                $where1 = ' and '. $querytitle . ' = ' . $queryvalue;
            } else{
                $where1 = '';
            }

            if ($querycity) {
                $where2 = ' and address1 = ' . "'$querycity'";
            }
            $select = 'id,username,role,city,status';
            $sql2 = "SELECT $select FROM `adminuser` 
                      WHERE 1=1 $where1 $where2 ORDER BY id ASC"; //顺序改动
            $result2 = mysqli_query($connect, $sql2);
            $items = array();
            while ($data = mysqli_fetch_assoc($result2)) {

                array_push($items, $data);
            }
            $result['total'] = count($items);
            $items = array_chunk($items, $querylimit);
            $result['items'] = $items[$querypage - 1];
            echo callback("success", $result);
        } else {
            //无用户
            echo callback("false", "未查询到管理员");
        }
    }



    //功能：更改管理员状态/删除管理员，计入log
    //入参：$session：管理员的session；  $id：管理员的id；  $type：操作类型(可选：changestatus/delete)；  $adminname：操作人的name
    //出参：success: 操作成功；      false:  session已过期
    public function modifyinfo($session,$id,$type,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $data = 'adminuser';
        $time = date("Y-m-d H:i:s",time());
        $sql2 = "select `id`,`status` from `adminuser` WHERE id = '$id' ";
        $result2 = mysqli_query($connect,$sql2);
        $array = mysqli_fetch_row($result2);
        $id = $array[0];
        $status = $array[1];
        switch ($type){
            //更改管理员状态（0-1）
            case 'changestatus':
                $type = "update";
                if ($status == 1){
                    $sql3 = "update `adminuser` set status = 0 WHERE id = '$id' ";
                    $result3 = mysqli_query($connect,$sql3);
                    $description = "更改管理员status为0。"."管理员编号："."$id";
                    $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                    $result3_m = mysqli_query($connect,$sql3_m);
                    echo (callback("success","操作成功"));
                }else{
                    $sql4 = "update `adminuser` set status = 1 WHERE id = '$id' ";
                    $result4 = mysqli_query($connect,$sql4);
                    $description = "更改管理员status为1。"."管理员编号："."$id";
                    $sql4_m = "INSERT into log(data,description,type,id,time) VALUES ('$data','$description','$type','$adminname','$time')";
                    $result4_m = mysqli_query($connect,$sql4_m);
                    echo (callback("success","操作成功"));
                }
                break;
            //删除此管理员
            case 'delete':
                $sql5 = "delete from `adminuser` WHERE id = '$id' ";
                $result5 = mysqli_query($connect,$sql5);
                $description = "删除此管理员。"."管理员编号："."$id";
                $sql5_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                $result5_m = mysqli_query($connect,$sql5_m);
                //id实现上移补全
                $sql_n = "SELECT * FROM `adminuser` WHERE 1";
                $result_n = mysqli_query($connect,$sql_n);
                $number = mysqli_num_rows($result_n);
                $number = $number - $id + 1 ;
                for (;$number > 0;$number--){
                    $old_id = $id + 1;
                    $sql = "update `adminuser` set id = '$id' WHERE id = '$old_id' ";
                    $result = mysqli_query($connect,$sql);
                    $id++;
                }
                echo (callback("success","操作成功"));
                break;
        }
    }



    //功能：修改管理员信息
    //入参：$session：管理员的session；
    //      $form: 一个键值对形式的数组，键分别为：  username, avatarurl
    //出参：success: 操作成功；      false:无此管理员  session已过期
    public function modifydetail($session,$form){
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }
        $form = json_decode($form);
        //传回的值
        $username = $form -> username;
        $avatarurl = $form -> avatarurl;
        $sql = "select * from `adminuser` WHERE username = '$username' ";
        $result = mysqli_query($connect,$sql);
        if (mysqli_num_rows($result) == 0){
            exit(callback("false","无此管理员"));
        }
        //数据库查到的值
        $sql1 = "select avatarurl from `adminuser` WHERE username = '$username' ";
        $result1 = mysqli_query($connect,$sql1);
        $array1 = mysqli_fetch_row($result1);
        $old_avatarurl = $array1[0];
        if ($avatarurl == $old_avatarurl){
            exit(callback("success","操作成功"));
        }else{
            $sql3 = "update `adminuser` set avatarurl = '$avatarurl' WHERE username = '$username' ";
            $result3 = mysqli_query($connect,$sql3);
            echo (callback("success","操作成功"));
        }
    }


}