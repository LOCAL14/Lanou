<?php
/**
 * Created by PhpStorm.
 * User: 杨一鸣
 * Date: 2018/1/26
 * Time: 16:15
 */

class session{

    //功能：通过sessioncode将用户的openid和sessioncode插入到redis设置有效期60天
    //入参：用户$session $openid $session_key
    //出参：无
    public function newsession($sessioncode,$openid,$session_key){
        $array = array("$openid","$session_key");
        $jsona = json_encode($array);

        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->set($sessioncode,$jsona, 5184000);
    }



    //功能：通过sessioncode验证用户的session是否过期
    //入参：用户$session
    //出参：success: $openid  ;    false: false
    public  function checksession($session){
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $jsonb = $redis->get($session);
        $array = json_decode($jsonb);
        $openid = $array[0];
        $session_key = $array[1];
        if (empty($openid) && empty($session_key)){
            return false;
//            die(callback("false","session已过期"));
        }else{
            return $openid;
        }
    }

    //功能：登出账户
    //入参：用户$session
    //出参：success: $openid  ;    false: false
    public  function deletesession($session){
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $jsonb = $redis->get($session);
        $array = json_decode($jsonb);
        $openid = $array[0];
        $session_key = $array[1];
        if (empty($openid) && empty($session_key)){
            $redis->del($session);
            return true;
        }else{
            return true;
        }
    }


}