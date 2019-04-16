<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 23:19
 */

namespace app\admin\model;

use think\Db;
use think\Model;

class postParise extends Model
{
    public static function in($list){
        try{
            Db::table("post_parise")->insert($list);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function del($idList){
        try{
            Db::table("post_parise")->whereIn('id',$idList)->update(array('status'=>1));
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function upd($id,$list){
        try{
            Db::table("post_parise")->where('id',$id)->update($list);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function read($list){
        $sql = "select id,userId,postId,create_time,update_time from post_parise where status=0";
        if(isset($list["id"])){
            $sql .= " AND id=".$list["id"];
        }else if(isset($list["userId"])){
            $sql .= " AND userId=".$list["userId"];
        }else if(isset($list["postId"])){
            $sql .= " AND postId=".$list["postId"];
        }
        $result = Db::query($sql);
        return $result;
    }
}