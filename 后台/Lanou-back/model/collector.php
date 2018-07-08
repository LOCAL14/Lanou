<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/2/20
 * Time: 上午1:03
 */
require_once 'base.php';
require_once 'session.php';
require_once '../controller/callback.php';

class collector
{
    //读取身份证的信息
    public function getcardinfo($session, $cardurl)
    {
        global $connect;
        $sessioncode = new session();
        $openid = false;
        $openid = $sessioncode->checksession($session);
        if (!$openid) {
            exit(callback("false", "session已过期"));
        }

        //以下为Tencent-AI的接口函数
        function getReqSign($params /* 关联数组 */, $appkey /* 字符串*/)
        {
            // 1. 字典升序排序
            ksort($params);
            // 2. 拼按URL键值对
            $str = '';
            foreach ($params as $key => $value) {
                if ($value !== '') {
                    $str .= $key . '=' . urlencode($value) . '&';
                }
            }
            // 3. 拼接app_key
            $str .= 'app_key=' . $appkey;
            // 4. MD5运算+转换大写，得到请求签名
            $sign = strtoupper(md5($str));
            return $sign;
        }

        function doHttpPost($url, $params)
        {
            $curl = curl_init();

            $response = false;
            do {
                // 1. 设置HTTP URL (API地址)
                curl_setopt($curl, CURLOPT_URL, $url);
                // 2. 设置HTTP HEADER (表单POST)
                $head = array(
                    'Content-Type: application/x-www-form-urlencoded'
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER, $head);
                // 3. 设置HTTP BODY (URL键值对)
                $body = http_build_query($params);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
                // 4. 调用API，获取响应结果
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_NOBODY, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($curl);
                if ($response === false) {
                    $response = false;
                    break;
                }
                $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($code != 200) {
                    $response = false;
                    break;
                }
            } while (0);
            curl_close($curl);
            return $response;
        }

        //获取图片，按照Tencent-AI要求转换为base64编码
        $resource = $cardurl;
        $data = file_get_contents($resource);
        $base64 = base64_encode($data);         //使用vardump调试base64时 Chrome会出不来数据

        //按照Tencent-AI要求设置参数
        $appkey = 'P3K6PLtRpqrazYjj';
        $params = array(
            'app_id' => '1106663513',
            'image' => $base64,
            'card_type' => '0',
            'time_stamp' => strval(time()),
            'nonce_str' => strval(rand()),
            'sign' => '',
        );
        $params['sign'] = getReqSign($params, $appkey);

        // 执行API调用
        $url = 'https://api.ai.qq.com/fcgi-bin/ocr/ocr_idcardocr';
        $response = doHttpPost($url, $params);

        //判断执行结果
        if ($response . ret == 0) {
            $response = json_decode($response);
            $temp = array();
            $temp['name'] = $response->data->name;
            $temp['sex'] = $response->data->sex;
            $temp['birth'] = $response->data->birth;
            $temp['address'] = $response->data->address;
            $temp['id'] = $response->data->id;
            echo callback('success', $temp);
        } else if ($response . ret == 16432) {
            $msg = '提交的图片有误';
            echo callback('false', $msg);
        } else {
            $msg = $response . ret;
            echo callback('false', $msg);
        }

    }

    //功能：按照条件抓取对应的用户列表;
    //入参：用户$session，$listquery（查询条件json数组）
    //出参：success:数组[items,total]     ；   false:session已过期
    public function fetchcollector($session, $listquery)
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
            if ($querytitle == 'phonenumber') {
                $where1 = ' and '. $querytitle . ' = ' . $queryvalue;
            } else if ($querytitle == 'name') {
                $where1 = ' and '. $querytitle . ' = ' . "'$queryvalue'";
            } else if ($querytitle == 'neighborhood') {
                $where1 = ' and  address2 = ' . "'$queryvalue'";
            } else{
                $where1 = '';
            }

            if ($querycity) {
                $where2 = ' and address1 = ' . "'$querycity'";
            }
            $select = 'collectorid,phonenumber,password,name,sex,birthday,address1,address2,idcard_number,idcard_url,
            idcard_address,status,register_time,score';
            $sql2 = "SELECT $select FROM `collector` 
                      WHERE 1=1 $where1 $where2 ORDER BY collectorid ASC"; //顺序改动
            $result2 = mysqli_query($connect, $sql2);
            $items = array();
            while ($data = mysqli_fetch_assoc($result2)) {

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


    //功能：验证用户的短信校验码是否正确，注册新回收员。并记录此操作。
    //入参：session ;
    //      form:数组11项，是键值对的形式。数组包含了注册城市社区，账户信息，身份证信息
    //           {city: ‘’,neighborhood: ‘’,phonenumber: ‘’,smscode: ‘’,password: ‘’,cardUrl: ‘’,name: ‘’,sex: ‘’,birth: ‘’,address: ‘’,cardid: ‘’,} ;
    //      adminname: 操作者的username
    //出参：success: 操作成功   ;   false:校验码不正确   session已过期
    public function newcollector($session,$form,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $time = date("Y-m-d H:i:s",time());
        //将$form数组解码
        $form = json_decode($form);
        $thecode = $form -> smscode;
        $phonenumber = $form -> phonenumber;
        $query2 = "select * from smscode where phonenumber = '$phonenumber' and code = '$thecode' ";
        $result2 = mysqli_query($connect,$query2);
        if (mysqli_num_rows($result2) > 0){
            $password = $form -> password;
            $name = $form -> name;
            $sex = $form -> sex;
            $birthday = $form -> birth;
            $address1 = $form -> city;
            $address2 = $form -> neighborhood;
            $idcard_number = $form -> cardid;
            $idcard_url = $form -> cardUrl;
            $idcard_address = $form -> address;
            $register_time = $time;
            $sql2 = "insert into collector(phonenumber,password,name,sex,birthday,address1,address2,idcard_number,idcard_url,idcard_address,register_time)
                 VALUES 
                 ('$phonenumber','$password','$name','$sex','$birthday','$address1','$address2','$idcard_number','$idcard_url','$idcard_address','$register_time')";
            $result2 = mysqli_query($connect,$sql2);
            //写入log
            $data = 'collector';
            $description = "注册新回收员。"."姓名："."$name"."。电话号："."$phonenumber";
            $type = "add";
            $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
            $result3_m = mysqli_query($connect,$sql3_m);
            echo (callback("success","操作成功"));
        }else{
            exit(callback("false","校验码不正确"));
        }
    }



    //功能：更改回收员状态（仅限0/1）或删除回收员，并将操作写入log
    //入参：$session:用户session;  $collectorid:回收员id;  $type:类型;  $adminname:操作者的username
    //出参：success: 操作成功   ;   false:无此回收员   session已过期
    public function modifyinfo($session,$collectorid,$type,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql1 = "select * from `collector` WHERE collectorid = '$collectorid' ";
        $result1 = mysqli_query($connect,$sql1);
        if ( mysqli_num_rows($result1) == 0){
            echo (callback("false","无此回收员"));
        }
        $time = date("Y-m-d H:i:s",time());
        $sql2 = "select `phonenumber`,`name`,`status` from `collector` WHERE collectorid = '$collectorid' ";
        $result2 = mysqli_query($connect,$sql2);
        $array = mysqli_fetch_row($result2);
        $phonenumber = $array[0];
        $name = $array[1];
        $status = $array[2];
        switch ($type){
            //删去回收员
            case 'delete':
                $sql3 = "delete from `collector` WHERE collectorid = '$collectorid' ";
                $result3 = mysqli_query($connect,$sql3);
                $data = 'collector';
                $description = "删去回收员。"."姓名："."$name"."，电话号："."$phonenumber";
                $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                $result3_m = mysqli_query($connect,$sql3_m);
                echo (callback("success","操作成功"));
                break;
            //改变回收员状态
            case 'changestatus':
                $type = "update";
                $data = 'collector';
                if ($status == 1){
                    $sql4 = "update `collector` set status = 0 WHERE collectorid = '$collectorid' ";
                    $result4 = mysqli_query($connect,$sql4);
                    $description = "改变回收员status为0。"."姓名："."$name"."，电话号："."$phonenumber";
                    $sql4_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                    $result4_m = mysqli_query($connect,$sql4_m);
                    echo (callback("success","操作成功"));
                }else{
                    $sql5 = "update `collector` set status = 1 WHERE collectorid = '$collectorid' ";
                    $result5 = mysqli_query($connect,$sql5);
                    $description = "改变回收员status为1。"."姓名："."$name"."，电话号："."$phonenumber";
                    $sql5_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
                    $result5_m = mysqli_query($connect,$sql5_m);
                    echo (callback("success","操作成功"));
                }
                break;
        }
    }



    //功能：修改回收员的个人信息，并将操作写入log
    //入参：$session:用户session;
    //      $collectorid:回收员id;
    //      $form: 形式与SELECT collectorid,phonenumber,password,name,sex,birthday,address1,address2,idcard_number,idcard_url,idcard_address,status,register_time,score FROM collector 的mysqli_fetch_assoc结果相同，
    //      $adminname:操作者的username
    //出参：success: 操作成功   ;   false:无此回收员   session已过期
    public function modifydetail($session,$collectorid,$form,$adminname){
        global $connect;
        $sessioncode = new session();
        $openid= false;
        $openid = $sessioncode->checksession($session);
        if(!$openid){
            exit(callback("false","session已过期"));
        }
        $sql1 = "select * from `collector` WHERE collectorid = '$collectorid' ";
        $result1 = mysqli_query($connect,$sql1);
        if ( mysqli_num_rows($result1) == 0){
            exit(callback("false","无此回收员"));
        }
        $form = json_decode($form);
        $phonenumber = $form -> phonenumber;
        $password = $form -> password;
        $name = $form -> name;
        $sex = $form -> sex;
        $birthday = $form -> birthday;
        $address1 = $form -> address1;
        $address2 = $form -> address2;
        $idcard_number = $form -> idcard_number;
        $idcard_url = $form -> idcard_url;
        $idcard_address = $form -> idcard_address;
        $score = $form -> score;

        $sql2 = "SELECT 
                 phonenumber,password,name,sex,birthday,address1,address2,idcard_number,idcard_url,idcard_address,status,register_time,score 
                 FROM `collector` 
                 WHERE collectorid = '$collectorid' ";
        $result2 = mysqli_query($connect,$sql2);
        $array = mysqli_fetch_assoc($result2);
        $old_phonenumber = $array['phonenumber'];
        $old_password = $array['password'] ;
        $old_name = $array['name'];
        $old_sex = $array['sex'];
        $old_birthday = $array['birthday'];
        $old_address1 = $array['address1'];
        $old_address2 = $array['address2'];
        $old_idcard_number = $array['idcard_number'];
        $old_idcard_url = $array['idcard_url'];
        $old_idcard_address = $array['idcard_address'];
        $old_score = $array['score'];
        if ($phonenumber == $old_phonenumber
            && $password == $old_password
            && $name == $old_name
            && $sex == $old_sex
            && $birthday == $old_birthday
            && $address1 == $old_address1
            && $address2 == $old_address2
            && $idcard_number == $old_idcard_number
            && $idcard_url == $old_idcard_url
            && $idcard_address == $old_idcard_address
            && $score == $old_score
        ){
            exit(callback("success","操作成功"));
        }else{
            $sql3 = "update `collector` 
                     set
                     phonenumber = '$phonenumber',password = '$password',name = '$name',sex = '$sex',birthday ='$old_birthday',address1 = '$address1',address2 = '$address2',idcard_number = '$idcard_number',idcard_url = '$idcard_url',idcard_address = '$idcard_address',score = '$score' 
                     WHERE collectorid = '$collectorid' ";
            $result3 = mysqli_query($connect,$sql3);
            $type = "update";
            $data = 'collector';
            $time = date("Y-m-d H:i:s",time());
            $description = "修改回收员的个人信息。"."原姓名："."$old_name"."，原电话号："."$old_phonenumber";
            $sql3_m = "INSERT into log(data,description,type,adminname,time) VALUES ('$data','$description','$type','$adminname','$time')";
            $result3_m = mysqli_query($connect,$sql3_m);
            echo (callback("success","操作成功"));
        }
    }



}