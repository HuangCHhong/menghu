<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 21:33
 */

namespace app\common\model;


use think\Model;
use think\Session;

class Common extends Model
{
    // 设置session
    public static function setSession($key,$value){
        \think\facade\Session::set($key,$value);
    }

    // 获取session
    public static function getSession($key){
        \think\facade\Session::get($key);
    }

    public static function hasSesssion($key){
        return \think\facade\Session::has($key);
    }

    // sql 语句拼装
    public static function generateSQL($idList){
        $str = "(";
        foreach ($idList as $id){
            $str .= $id.',';
        }
        $str .= ")";
        return $str;
    }
}