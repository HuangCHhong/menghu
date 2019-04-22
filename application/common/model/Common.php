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
    // 获取session_id
    public static function sessionId(){
//        if (PHP_SESSION_ACTIVE != session_status()) {
//            session_start();
//        }
        return session_id();
    }

    // 设置session
    public static function setSession($key,$value,$flag=true){
        //获取session_id
        if($flag){
            $session_id = Access::MustParamDetect('session_id');
            session_id($session_id);
        }
//        session_start();
//        $_SESSION[$key] = $value;
        \think\facade\Session::set($key,$value);
    }

    // 获取session
    public static function getSession($key){
        //获取session_id
        $session_id = Access::MustParamDetect('session_id');
        session_id($session_id);
//        session_start();
//        return $_SESSION[$key];
        return \think\facade\Session::get($key);
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
        $str = rtrim($str,",");
        $str .= ")";
        return $str;
    }
}