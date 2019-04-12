<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/9
 * Time: 8:53
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class weight extends Model
{
    public static function getById($typeId,$roleId){
        $result = Db::table("weight")->where("typeId",$typeId)->where("roleId",$roleId)->findOrEmpty();
        return $result;
    }

    public static function read($list){
        $sql = "select id,typeId,roleId,weight from weight where 1";
        if(isset($list["typeId"])){
            $sql .= " AND typeId = ".$list["typeId"];
        }
        if(isset($list["roleId"])){
            $sql .= " AND roleId =".$list["roleId"];
        }
        return Db::query($sql);
    }
}