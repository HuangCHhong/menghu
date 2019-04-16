<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 22:30
 */

namespace app\admin\model;


use app\common\model\Common;
use app\common\model\Access;
use think\Model;
use think\Db;

class Post extends Model
{
    public static function getById($id){
        try{
            $result =  Db::table("post")->where('id',$id)->where('status',0)->findOrFail();
            return $result;
        }catch (\Exception $e){
            Access::Respond(0,array(),"查询帖子失败");
        }
    }

    public static function in($list){
        try{
            return Db::table("post")->insertGetId($list);
        }catch (\Exception $e){
            Access::Respond(0,array(),"添加帖子失败");
        }
    }

    public static function del($postIdList){
        try{
            Db::table("post")->whereIn('id',$postIdList)->update(array('status'=>1));
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function upd($postId,$list){
        try{
            Db::table("post")->where('id',$postId)->update($list);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function read($list){
        $sql = "select id,userId,content,praise,anonymous,typeId,create_time,update_time from post where status=0";
        if(isset($list["id"])){
            $sql .= " AND id=".$list["id"];
        }else if(isset($list["idList"])){
           $str = Common::generateSQL($list["idList"]);
            $sql .= " AND id In ".$str;
        }else if(isset($list["typeId"])){
            $sql .= "AND typeId=".$list["typeId"];
        }else if(isset($list["userId"])){
            $sql .= " AND userId=".$list["userId"];
        }else if(isset($list["firstTime"])){
            $sql .= " AND create_time > ".$list["firstTime"];
        }else if(isset($list["endTime"])){
            $sql .= " AND create_time <".$list["endTime"];
        }else if(isset($list["content"])){
            $sql .= " AND content like '%".$list["content"]."%'";
        }

        $result = Db::query($sql);
        return $result;
    }

    // 点赞数递增
    public static function addParise($postId){
        $data = self::getById($postId);
        $data["praise"]++;
        return self::upd($data["id"],$data);
    }
    // 点赞数递减
    public static function delParise($postId){
        $data = self::getById($postId);
        $data["praise"]--;
        return self::upd($data["id"],$data);
    }

}