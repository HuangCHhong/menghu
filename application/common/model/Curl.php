<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/8/15
 * Time: 14:30
 */

namespace app\common\model;


use think\Model;

class Curl extends Model
{
    public static function sendpost($url,$postData){

        $ch=curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        //post的变量
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postData);

        //是否进行证书验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $output=curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public static function sendget($url,$queryParam){
        //拼装url
        $str = "";
        foreach ($queryParam as $key=>$value){
            $str .= "$key=$value&";
        }
        if(!empty($str)){
            $url = trim($url.'?'.$str,'&');
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);

        //是否进行证书验证
         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}