<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/9
 * Time: 0:13
 */

namespace app\admin\controller;


use think\Controller;
use app\common\model\Access;
use app\common\model\Authority;
use app\admin\model\infoType as infoTypeModel;
class InfoType extends Controller
{
    // 获取消息类型
    public function get(){
        // 权限设置
        Authority::getInstance()->permitAll(true)->check(null);
        Access::Respond(1,infoTypeModel::getAll(),"消息获取成功");
    }
}