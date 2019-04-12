<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/9
 * Time: 0:06
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class infoType extends Model
{
    public static function getAll(){
        return Db::table("info_type")->select();
    }
}