<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 22:57
 */

namespace app\admin\model;


use app\common\model\Common;
use think\Model;
use think\db;
class relationship extends Model
{
    public static function in($list){
        try{
            Db::table("relationship")->insert($list);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function del($idList){
        try{
            Db::table("relationship")->whereIn('id',$idList)->update(array('status'=>1));
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function upd($id,$list){
        try{
            Db::table("relationship")->where('id',$id)->update($list);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function read($list){
        $sql = "select id,userId,attUserId,create_time,update_time from relationship where status=0";
        if(isset($list["userIdList"])){
            $str = Common::generateSQL($list["userIdList"]);
            $sql .= " AND userId In ".$str;
        }
        if(isset($list["attUserIdList"])){
            $str = Common::generateSQL($list["attUserIdList"]);
            $sql .= " AND attUserId In ".$str;
        }
        if(isset($list["id"])){
            $sql .= " AND id =".$list["id"];
        }
        $result = Db::query($sql);
        return $result;
    }

    public static function getByUserId($userId,$attUserId){
        return Db::table("relationship")->where("userId",$userId)->where("attUserId",$attUserId)->find();
    }
}