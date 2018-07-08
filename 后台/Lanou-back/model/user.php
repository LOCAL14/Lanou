<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/1/26
 * Time: 15:39
 */

require_once 'base.php';
require_once '../controller/callback.php';
require_once 'session.php';
require_once 'smscode.php';

//建立名字为user的对象
class user
{
    //功能：生成每个用户的唯一标识并给前端传回session值
    //入参：微信前端调用api接口生成的wxcode即为$code
    //出参：  success:标识用户的随机数session
    public function createsession($code){
        //向微信服务器发送请求
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=wx41c0441ac2a464a0&secret=ef6e33d468dff880dc53ad7bc3d692e2&js_code=$code&grant_type=authorization_code";
        $json_src = file_get_contents($url);
        //将json解码
        $json_src = json_decode($json_src,true);
        $openid = $json_src['openid'];
        $session_key = $json_src['session_key'];
        //生成随机数满足2^128
        //code就是3rd_session
        $code = rand(1000000000,9999999999);//如何生成一个不重复的code值？？？
        //通过sessioncode将用户的openid和sessioncode插入到redis设置有效期15天
        $setsession = new session();
        $setsession -> newsession($code,$openid,$session_key);
        //发送code给前端
        echo (callback("success","$code"));
    }



    //功能：验证用户是否注册 未注册则插入数据库
    //入参：$session, $phonenumber, $thecode, $wxnickname, $wxavatarurl
    //出参：success:注册成功    ;     false:验证码不正确
    public function newuser($session, $phonenumber, $thecode, $wxnickname, $wxavatarurl){
        global $connect;
        $rigistertime = time();
        //$sessioncode是类session的一个实例
        $sessioncode = new session();
        $openid = $sessioncode->checksession($session);
        $query1 = "select * from user where openid = '$openid' ";
        $result1 = mysqli_query($connect, $query1);
        if (mysqli_num_rows($result1) > 0) {
            exit(callback("success", "此用户已注册"));
        }
        //验证输入的验证码是否正确返回ture，false
        $smscode = new smscode();
        $result = false;
        $result = $smscode->verifycode($phonenumber, $thecode);
        if ($result) {
            $query3 = "insert into user (openid,phonenumber,wxnickname,wxavatarurl,registertime) values ('$openid','$phonenumber','$wxnickname','$wxavatarurl','.$rigistertime.')";
            $result3 = mysqli_query($connect, $query3);
            echo callback("success", "注册成功");
        } else {
            exit (callback("false", "验证码不正确"));
        }
    }

    //查看用户是否注册，如果注册则获取用户的个人信息
    //入参：用户$session
    //出参：success:返回数组 $array 0-头像url 1-用户昵称 2-脱敏后的手机号  ；false:用户未注册
    public function getuserinfo($session){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        $query1 = "select * from `user` where openid = '$openid' ";
        $result1 = mysqli_query($connect, $query1);
        if (mysqli_num_rows($result1) == 0) {
            exit(callback("success", "用户未注册"));
        }
        $sql1 = "SELECT `phonenumber`, `wxnickname`, `wxavatarurl` FROM `user` WHERE status = 1 and openid = '$openid' ";
        $result1 = mysqli_query($connect,$sql1);
        $result1 = mysqli_fetch_row($result1);
        $phonenumber = $result1[0];
        $wxnickname = $result1[1];
        $wxavatarurl = $result1[2];
        $array = array("$wxavatarurl","$wxnickname","$phonenumber");
        echo (callback("success",$array));
    }

    //功能：查询用户余额
    //入参：用户$session
    //出参：success: money  ;    false:session已过期
    public function getusermoney($session){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $money = 0;
        $result1 = mysqli_query($connect, "select money from user WHERE openid = '$openid' ");
        $money = mysqli_fetch_row($result1);
        $money = $money[0];
        echo(callback("success", "$money"));
    }



    //功能：查询用户手机号首尾号
    //入参：用户$session
    //出参： success：手机号格式为：176****6681  ； false： session已过期
    public function getphonenumber($session){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql1 = "select phonenumber from `user` where openid = '$openid' ";
        $result1 = mysqli_query($connect,$sql1);
        $array = mysqli_fetch_row($result1);
        $phonenumber = $array[0];
        $num = strval($phonenumber);
        //隐藏手机号4-7n
        $num = substr_replace($num,'****',3,4);
        echo (callback("success",$num));
    }



    //功能：更改用户手机号
    //入参：$session,$phonenumber,$thecode
    //出参：success:手机号更改成功 ;     false: 验证码错误    false: session已过期
    public  function changephone($session,$newphonenumber,$thecode){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $result1 = mysqli_query($connect,"select code from `smscode` where phonenumber = '$newphonenumber' ");
        $array1 = mysqli_fetch_row($result1);
        $code = $array1[0];
        if ($code == $thecode){
            $result3 = mysqli_query($connect,"update `user` set phonenumber = '$newphonenumber' where openid = '$openid' ");
            echo callback("success","手机号更改成功");
        }else{
            echo callback("false","验证码错误");
        }
    }



    //功能：验证手机号是否匹配
    //入参：用户的phonenumber,$thecode
    //出参：false:手机号不同    ;   true:手机号相同
    public function compare_phonenumber($session,$newphonenumber){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        $result1 = mysqli_query($connect,"select phonenumber from `user` where openid = '$openid' ");
        $array1 = mysqli_fetch_row($result1);
        $oldphonenumber = $array1[0];
        if($newphonenumber == $oldphonenumber) {
            return true;
        }else{
            return false;
        }
    }



    //功能：将用户地址信息address的数组$address{省，市，区，省市区编码，小区，小区编码，楼号，楼号编码，详细地址，联系人，手机号}插入到数据库user表中;
    //入参：用户$session，$array{省，市，区，小区，楼号，详细地址，联系人，手机号}
    //出参：success:地址成功插入     ；   false:session已过期；false:地址重复
    public function addaddress($session,$array){
        global $connect;
        $s = new session();
        $openid = $s ->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        //取出已经存入的所有地址
        $result1 = mysqli_query($connect,"select address from `user` WHERE openid = '$openid' ");
        $address = mysqli_fetch_row($result1);
        //因为address为json数组，使用前需解码
        $address = json_decode($address[0]);
        //将post到的新地址解码成数组
        $array = json_decode($array);


        //查询省市区编码
        //获得省市区名称，格式：省,市,区（若为直辖市，格式为：市，区）
        //判断是否为直辖市
        if ($array[0] == $array[1]){
            $city_name = "$array[1]".","."$array[2]";
        }else{
            $city_name = "$array[0]".","."$array[1]".","."$array[2]";
        }
        ////根据名称查询省市区编码district_id，$district_id为string
        $result4=mysqli_query($connect,"select district_id from `district` WHERE name = '$city_name' ");
        $district_id=mysqli_fetch_row($result4);
        //将string转为int，方便以后使用
        $district_id=intval($district_id[0]);

        //查询小区编码
        //获得小区名称
        $neighborhood = $array[3];
        //根据名称查询小区编码neighborhood_id，$neighborhood_id为string
        $result5=mysqli_query($connect,"select neighborhood_id from `area` WHERE name = '$neighborhood' ");
        $neighborhood_id=mysqli_fetch_row($result5);
        //将string转为int
        $neighborhood_id=intval($neighborhood_id[0]);

        //查询楼号编码
        //获取楼号
        $building = $array[4];
        //根据小区名称查询楼号信息数组building_info
        $result6=mysqli_query($connect,"select building_info from `area` WHERE name = '$neighborhood' ");
        $building_info=mysqli_fetch_row($result6);
        //因为building_info为json数组，使用前需解码
        $building_info=json_decode($building_info[0]);
        //在楼号信息数组中查询楼号对应的数组下标，$temp为int
        $temp = array_search($building,$building_info);
        //将数组下标加一即为楼号编码
        $building_id = $temp + 1;


        //组成新的数组
        //省市区编码插入到数组中
        array_splice($array, 3, 0, $district_id);
        //小区编码插入到数组中
        array_splice($array, 5, 0, $neighborhood_id);
        //楼号编码插入到数组中
        array_splice($array, 7, 0, $building_id);


        //检查地址是否重复
        //在数组中查询重复项
        $repeat_item = array_search($array,$address);
        //如果存在重复项，退出并callback地址重复(由于$repeat_item可能为0，因此使用!==
        if($repeat_item !== false){
            exit(callback('false','地址重复'));
        }

        //将所有address整合成一个数组  格式$array{省，市，区，省市区编码，小区，小区编码，楼号，楼号编码，详细地址，联系人，手机号}
        //将新加入的地址放在数组最前面
        array_unshift($address,$array);
        //将数组json编码成字符串,JSON_UNESCAPED_UNICODE是为了防止出现乱码
        $address = json_encode($address,JSON_UNESCAPED_UNICODE);

        //数组信息更新到数据库address表
        $result2 = mysqli_query($connect,"update `user` set address = '$address' WHERE openid = '$openid' ");
        echo callback("success","地址成功插入");
    }



    //功能：查询用户全部地址
    //入参：用户$session
    //出参：success: address     ；   false:session已过期；false:无地址信息
    public function getaddress($session){
        global $connect;
        $s = new session();
        $openid = $s ->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql1 = "select address from `user` where openid = '$openid' ";
        $result1 = mysqli_query($connect,$sql1);
        $array = mysqli_fetch_row($result1);
        $address = json_decode($array[0]);
        if($address){
            echo callback('success',$address);
        }else{
            echo callback('false','无地址信息');
        }

    }



    //功能：更改用户某一地址;
    //入参：用户$session，$array{省，市，区，小区，楼号，详细地址，联系人，手机号}，$index（该地址在数组中的下标）
    //出参：success:地址成功修改     ；   false:session已过期；false:地址重复
    public function modifyaddress($session,$array,$index){
        global $connect;
        $s = new session();
        $openid = $s ->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        //取出已经存入的所有地址
        $result1 = mysqli_query($connect,"select address from `user` WHERE openid = '$openid' ");
        $address = mysqli_fetch_row($result1);
        //因为address为json数组，使用前需解码
        $address = json_decode($address[0]);
        //将post到的新地址解码成数组
        $array = json_decode($array);


        //查询省市区编码
        //获得省市区名称，格式：省,市,区（若为直辖市，格式为：市，区）
        //判断是否为直辖市
        if ($array[0] == $array[1]){
            $city_name = "$array[1]".","."$array[2]";
        }else{
            $city_name = "$array[0]".","."$array[1]".","."$array[2]";
        }
        ////根据名称查询省市区编码district_id，$district_id为string
        $result4=mysqli_query($connect,"select district_id from `district` WHERE name = '$city_name' ");
        $district_id=mysqli_fetch_row($result4);
        //将string转为int，方便以后使用
        $district_id=intval($district_id[0]);

        //查询小区编码
        //获得小区名称
        $neighborhood = $array[3];
        //根据名称查询小区编码neighborhood_id，$neighborhood_id为string
        $result5=mysqli_query($connect,"select neighborhood_id from `area` WHERE name = '$neighborhood' ");
        $neighborhood_id=mysqli_fetch_row($result5);
        //将string转为int
        $neighborhood_id=intval($neighborhood_id[0]);

        //查询楼号编码
        //获取楼号
        $building = $array[4];
        //根据小区名称查询楼号信息数组building_info
        $result6=mysqli_query($connect,"select building_info from `area` WHERE name = '$neighborhood' ");
        $building_info=mysqli_fetch_row($result6);
        //因为building_info为json数组，使用前需解码
        $building_info=json_decode($building_info[0]);
        //在楼号信息数组中查询楼号对应的数组下标，$temp为int
        $temp = array_search($building,$building_info);
        //将数组下标加一即为楼号编码
        $building_id = $temp + 1;



        //组成新的数组
        //省市区编码插入到数组中
        array_splice($array, 3, 0, $district_id);
        //小区编码插入到数组中
        array_splice($array, 5, 0, $neighborhood_id);
        //楼号编码插入到数组中
        array_splice($array, 7, 0, $building_id);


        //检查地址是否重复
        //在数组中查询重复项
        $repeat_item = array_search($array,$address);
        //如果存在重复项，退出并callback地址重复(由于$repeat_item可能为0，因此使用!==
        if($repeat_item !== false){
            exit(callback('false','地址重复'));
        }

        //将所有address整合成一个数组  格式$array{省，市，区，省市区编码，小区，小区编码，楼号，楼号编码，详细地址，联系人，手机号}
        //根据$index替换地址信息
        array_splice($address,$index, 1, [$array]);
        //将数组json编码成字符串,JSON_UNESCAPED_UNICODE是为了防止出现乱码
        $address = json_encode($address,JSON_UNESCAPED_UNICODE);

        //数组信息更新到数据库address表
        $result2 = mysqli_query($connect,"update `user` set address = '$address' WHERE openid = '$openid' ");
        echo callback("success","地址成功修改");
    }



    //功能：删除用户某一地址的信息;
    //入参：用户$session，$index（该地址在数组中的下标）
    //出参：success:地址成功删除     ；   false:session已过期
    public function deleteaddress($session,$index){
        global $connect;
        $s = new session();
        $openid = $s ->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        //取出已经存入的所有地址
        $result1 = mysqli_query($connect,"select address from `user` WHERE openid = '$openid' ");
        $address = mysqli_fetch_row($result1);
        //因为address为json数组，使用前需解码
        $address = json_decode($address[0]);

        //根据$index删除该地址
        array_splice($address,$index, 1);
        //将数组json编码成字符串,JSON_UNESCAPED_UNICODE是为了防止出现乱码
        $address = json_encode($address,JSON_UNESCAPED_UNICODE);

        //数组信息更新到数据库address表
        $result2 = mysqli_query($connect,"update `user` set address = '$address' WHERE openid = '$openid' ");
        echo callback("success","地址成功删除");
    }



    /*-------------------------以下为管理员面板功能------------------------------*/

    //功能：按照条件抓取对应的用户列表;
    //入参：用户$session，$listquery（查询条件json数组）
    //出参：success:数组[items,total]     ；   false:session已过期
    public function fetchusers($session,$listquery){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql1 = "select * from `user`";
        $result1 = mysqli_query($connect,$sql1);


        $listquery = json_decode($listquery);
        $querypage = $listquery -> page;
        $querylimit = $listquery -> limit;
        $querycity = $listquery -> city;
        $querytitle = $listquery -> title;
        $queryvalue = $listquery -> value;

        if (mysqli_num_rows($result1) > 0){
            //存在用户
            if($querytitle == 'phonenumber'){
                $where = 'WHERE '. $querytitle .' = '. $queryvalue;
            }else{
                $where = '';
            }

            $select = 'userid,phonenumber,wxnickname,status,registertime,creditscore,money,address';
            $sql2 = "SELECT $select FROM `user` 
                      $where ORDER BY userid ASC" ; //顺序改动
            $result2 = mysqli_query($connect,$sql2);
            $items = array();
            $result = array();
            while($data = mysqli_fetch_assoc($result2)){
                $address = json_decode($data['address']);
                $citystatus = 0;
                $neighborhoodstatus = 0;

                if($querycity){
                    foreach ($address as $addressitem){
                        if($addressitem[1] == $querycity){
                            $citystatus = 1;
                           break;
                        }
                    }
                }else{
                    $citystatus = 1;
                }

                if($querytitle == 'neighborhood'){
                    foreach ($address as $addressitem){
                        if($addressitem[4] == $queryvalue){
                            $neighborhoodstatus = 1;
                            break;
                        }
                    }
                }else{
                    $neighborhoodstatus = 1;
                }

                if($neighborhoodstatus && $citystatus){
                    $data['registertime'] = date("Y-m-d H:i",$data['registertime']);
                    if($address){
                        $tempcity = array();
                        $tempneighborhood = array();
                        foreach ($address as $addressitem){
                            array_push($tempcity,$addressitem[1]);
                            array_push($tempneighborhood,$addressitem[4]);
                        }
                        $tempcity = array_unique($tempcity);
                        $tempneighborhood = array_unique($tempneighborhood);
                        foreach ($tempcity as $temp){
                            $data['city'] = $data['city'] . ' ' . $temp;
                        }
                        foreach ($tempneighborhood as $temp){
                            $data['neighborhood'] = $data['neighborhood'] . ' ' . $temp;
                        }

                    }else{
                        $data['city'] = '无';
                        $data['neighborhood'] = '无';
                    }


                    $data['address'] = '';   //地址脱敏处理

                    array_push($items,$data);
                }

            }
            $result['total'] = count($items);
            $items = array_chunk($items ,$querylimit);
            $result['items'] = $items[$querypage - 1];
            echo callback("success",$result);
        }else{
            //无用户
            echo callback("false","未查询到用户");
        }
    }



    //功能：更改用户状态或删除用户，并将操作写入数据表
    //入参：session ; 用户ID userid ; 操作类型type ; 操作者名字adminname
    //出参：success：操作成功    ； false：无此用户  session已过期
    public function modifyinfo($session,$userid,$type,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql = "select * from `user` WHERE userid = '$userid' ";
        $result1 = mysqli_query($connect,$sql);
        if ( mysqli_num_rows($result1) == 0){
            echo (callback("false","无此用户"));
        }
        $time = date("Y-m-d H:i:s",time());
        $sql2 = "select `phonenumber`,`wxnickname`,`status` from `user` WHERE userid = '$userid' ";
        $result2 = mysqli_query($connect,$sql2);
        $array = mysqli_fetch_row($result2);
        $phonenumber = $array[0];
        $name = $array[1];
        $status = $array[2];
        switch ($type){
            //删去用户
            case 'delete':
                $sql3 = "delete from `user` WHERE userid = '$userid' ";
                $result3 = mysqli_query($connect,$sql3);
                $data = 'user';
                $description = "删去用户。"."昵称："."$name"."，电话号："."$phonenumber";
                $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                $result3_m = mysqli_query($connect,$sql3_m);
                echo (callback("success","操作成功"));
                break;
            //改变用户状态
            case 'changestatus':
                $data = 'user';
                $type = "update";
                if ($status == 1){
                    $sql4 = "update `user` set status = 0 WHERE userid = '$userid' ";
                    $result4 = mysqli_query($connect,$sql4);
                    $description = "改变用户status为0。"."昵称："."$name"."，电话号："."$phonenumber";
                    $sql4_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                    $result4_m = mysqli_query($connect,$sql4_m);
                    echo (callback("success","操作成功"));
                }else{
                    $sql5 = "update `user` set status = 1 WHERE userid = '$userid' ";
                    $result5 = mysqli_query($connect,$sql5);
                    $description = "改变用户status为1。"."昵称："."$name"."，电话号："."$phonenumber";
                    $sql5_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                    $result5_m = mysqli_query($connect,$sql5_m);
                    echo (callback("success","操作成功"));
                }
                break;
        }
    }




}