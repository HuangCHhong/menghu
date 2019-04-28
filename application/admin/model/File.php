<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/25
 * Time: 16:28
 */

namespace app\admin\model;


use think\Model;
use think\Db;
use app\common\model\Access;
class File extends Model
{
    public static  function in($data){
        try{
            return Db::table("file")->insertGetId($data);
        }catch (\Exception $e){
            Access::Respond(0,array(),"添加文件失败");
        }
    }

    public static function getById($id){
        return Db::table("file")->where("id",$id)->findOrFail();
    }

    //寻找还未备份的文件
    public static function getFile(){
        return Db::table("file")->where("isBackup",0)->select();
    }

    //更新文件
    public static function updateFilePath($data){
        $file = new File;
        return $file->saveAll($data);
//        return Db::table("file")->update($data);
    }
}