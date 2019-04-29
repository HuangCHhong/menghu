<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/29
 * Time: 9:59
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class PostFile extends Model
{
    public static function in($data){
        Db::table("post_file")->insertAll($data);
    }

    public static function getBypostId($postId){
        return Db::table("post_file")->where("postId",$postId)->select();
    }
}