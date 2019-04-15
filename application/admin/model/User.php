<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 21:55
 */

namespace app\admin\model;

use app\common\model\Access;
use app\common\model\Common;
use think\Db;
use think\Model;

class User extends Model
{
    public static function in($list){
        try{
            Db::table("user")->insert($list);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function upd($userId,$list){
        try{
            Db::table("user")->where('id',$userId)->update($list);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function read($openid){
        try{
           $result =  Db::table("user")->where('openId',$openid)->where('status',0)->find();
           if(count($result)<=0){
                return false;
           }
           return $result;
        }catch (\Exception $e){
            Access::Respond(0,array(),"查询用户失败");
        }
    }

    public static function getByUserId($userId){
        try{
            $result =  Db::table("user")->where('id',$userId)->where('status',0)->findOrFail();
            return $result;
        }catch (\Exception $e){
            Access::Respond(0,array(),"查询用户失败");
        }
    }

    public static function batchRead($idList){
        $sql = "select id,nickName,roleId,sex,city,praiseCount,avatarUrl,openId from user where status=0";
        if(!empty($openIdList)){
           $str = Common::generateSQL($idList);
            $sql .= " AND id In ".$str;
        }
        return Db::query($sql);
    }
}