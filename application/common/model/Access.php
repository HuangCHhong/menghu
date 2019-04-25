<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 9:14
 */
namespace app\common\model;
use app\common\model\Common;
use \Config;
class Access
{
    /**
     * 响应客户端
     */
    public static function Respond ($code, $data, $msg) {

        $mes = array();
        //添加sessionId
        $data['session_id'] = Common::sessionId();
        $mes['data']=$data;
        $mes['code']=$code;
        $mes['url']='';
        $mes['msg']=$msg;
        header('Content-Type: application/json');
        echo json_encode($mes, 256);
        exit();
    }

    /**
     * 必须参数的获取
     */
    public static function MustParamDetect ($key) {
        if (!input('?'.$key)) {
            Access::Respond (0, "", "缺少参数".$key);
        } else {
            /** 有传递值 */
            $value = "";
            if(isset($_POST[$key])) {
                $value = $_POST[$key];
            }
            else{
                $value=$_GET[$key];
            }
            return $value;
        }
    }

    /**
     * 对数组必须参数的获取
     */
    public static function MustParamDetectOfList ($listKeys) {
        // 必须参数
        foreach ($listKeys as $key=>$value){
            $listKeys[$value]=Access::MustParamDetect ($value);
        }
        return $listKeys;
    }

    /**
     * 可选参数的获取
     */
    public static function OptionalParam ($key) {
        if (!input('?'.$key)) {
            return null;
        } else {
            /** 有传递值 */
            $value = "";
            if(isset($_POST[$key])) {
                $value = $_POST[$key];
            }
            else{
                $value=$_GET[$key];
            }
            return $value;
        }
    }

    /**
     * rawData必选参数判断
    */
    public static function MustParamDetectOfRawData($listKeys,$data){
        $keyList = array_keys($data);
        foreach ($listKeys as $key){
            if(!in_array($key,$keyList)){
                Access::Respond (0, "", "缺少参数".$key);
            }
        }
        return true;
    }

    /**
     * 数组可选参数的获取
     */
    public static function OptionalParamOfList ($listKeys) {
        foreach ($listKeys as $key=>$value){
            $listKeys[$value]=Access::OptionalParam ($value);
        }
        return $listKeys;
    }

    /**
     * 输入格式校验
     */
    public static function CheckParamFormat ($content, $format='phone') {
        switch ($format) {
            case 'phone': {          // 手机格式
                /**
                 * 验证手机号码是否符合规则
                 */
                if (!is_numeric($content)) {
                    return false;
                }
                return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $content) ? true : false;
            } break;
            case 'date': {           // 日期格式
                if (date('Y-m-d', strtotime($content)) == $content) {
                    return true;
                } else {
                    return false;
                }
            } break;
            default: {               // 是否存在非法字符
               return true;
            } break;
        }
    }

    /**
     * 将数组转换成json格式
     */
    public static function json_arr($arr){
        header('Content-Type: application/json');
        return json_encode($arr, 256);
    }

    /**
     * 将json格式转换为数组格式
     */
    public static function deljson_arr($json){
        header('Content-Type: application/json');
        return json_decode($json, 256);
    }

    /**
     * 获取对应的拓展名规则
     */
    public static function getFileExtendConf ($type = "icon") {
        $obj = json_decode(Config::get("FILE_TYPE"), true);

        if (isset ($obj[$type])) {
            // 判断是否包括
            $tmp_arr = $obj[$type];
            if ($tmp_arr != null) {
                return $tmp_arr;
            }
            return ['size'=>0,'ext'=>'jpg'];
        } else {
            return ['size'=>0,'ext'=>'jpg'];
        }
        return ['size'=>0,'ext'=>'jpg'];
    }
}