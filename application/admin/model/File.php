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
//        try{
            return Db::table("file")->insertGetId($data);
//        }catch (\Exception $e){
//            Access::Respond(0,array(),"添加文件失败");
//        }
    }
}