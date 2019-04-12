<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/12
 * Time: 11:31
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class BlackList extends Model
{
    public static function getAll(){
        return Db::table("blackList")->select();
    }
}