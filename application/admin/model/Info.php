<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 22:28
 */

namespace app\admin\model;

use app\common\model\Access;
use think\Db;
use think\Model;
use app\common\model\Common;
class Info extends Model
{
    public static function in($list){
        try{
            $idList = array();
            foreach ($list as $data){
                $id = Db::table("info")->insertGetId($data);
                array_push($idList,$id);
            }
            return $idList;
        }catch (\Exception $e){
            Access::Respond(0,array(),"添加资讯失败");
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
        }else if(isset($list["idList"])){
            $str = Common::generateSQL($list["idList"]);
            $sql .= " AND id In ".$str;
        }else if(isset($list["title"])){
            $sql .= " AND title like '%".$list["title"]."%'";
        }
        $result = Db::query($sql);
        return $result;
    }

}