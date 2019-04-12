<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 22:28
 */

namespace app\admin\model;

use think\Db;
use think\Model;

class Info extends Model
{
    public static function in($list){
        try{
            Db::table("info")->data($list)->limit(100)->insertAll();
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function del($infoIdList){
        try{
            Db::table("info")->whereIn('id',$infoIdList)->update(array('status'=>1));
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function upd($infoId,$list){
        try{
            Db::table("info")->where('id',$infoId)->update($list);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function read($list){
        $sql = "select id,typeId,title,content,create_time,update_time from info where status=0";
        if(isset($list["id"])){
            $sql .= " AND id=".$list["id"];
        }else if(isset($list["typeId"])){
            $sql .= " AND typeId=".$list["typeId"];
        }else if(isset($list["firstTime"])){
            $sql .= " AND create_time > ".$list["firstTime"];
        }else if(isset($list["endTime"])){
            $sql .= " AND create_time <".$list["endTime"];
        }
        $result = Db::query($sql);
        return $result;
    }
}