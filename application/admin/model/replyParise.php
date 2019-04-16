<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 23:48
 */

namespace app\admin\model;


use think\Model;
use think\db;
class replyParise extends Model
{
    public static function in($list){
        try{
            Db::table("reply_parise")->insert($list);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function del($idList){
        try{
            Db::table("reply_parise")->whereIn('id',$idList)->update(array('status'=>1));
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function upd($id,$list){
        try{
            Db::table("reply_parise")->where('id',$id)->update($list);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function read($list){
        $sql = "select id,userId,replyId,create_time,update_time from reply_parise where status=0";
        if(isset($list["id"])){
            $sql .= " AND id=".$list["id"];
        }else if(isset($list["userId"])){
            $sql .= " AND userId=".$list["userId"];
        }else if(isset($list["replyId"])){
            $sql .= " AND replyId=".$list["replyId"];
        }
        $result = Db::query($sql);
        return $result;
    }
}