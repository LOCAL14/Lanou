<?php
/**
 * Created by PhpStorm.
 * User: xiazhen
 * Date: 2018/1/26
 * Time: 下午5:58
 */
require_once 'base.php';
require_once '../controller/callback.php';
class homepage{
    //功能：查询首页轮播广告的url
    //入参：无
    //出参：success: 包含所有轮播广告的url的数组
    public function ad_fetch(){
       global $connect;
       function fetch2array($sql_result){
            $result = array();
            while($data = mysqli_fetch_assoc($sql_result)){
                $temp = $data['url'];              //只传出url
                array_push($result,$temp);
            }
            return $result;
        };

        $sql1 = "select url from homead_goods where status = 1 ";
        $sql1_result = mysqli_query($connect,$sql1);
        $array = fetch2array($sql1_result);
        echo callback("success",$array);

    }

    //功能：查询所有废品信息
    //入参：无
    //出参：success: 包含所有废品的废品信息的数组
    public function goods_fetch(){
        global $connect;

        function fetch2array($sql_result){
            $result = array();
            while($data = mysqli_fetch_assoc($sql_result)){
                array_push($result,$data);        //全部数据库内容传出
            }
            return $result;
        };

        $sql1 = "select * from homepage_goods where status = 1 ";
        $sql1_result = mysqli_query($connect,$sql1);
        $array = fetch2array($sql1_result);
        echo callback("success",$array);


    }
}