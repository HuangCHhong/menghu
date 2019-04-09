<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/9
 * Time: 0:13
 */

namespace app\admin\controller;


use app\common\model\Access;
use think\Controller;
use app\common\model\Authority;
use app\admin\model\Role as RoleModel;
class Role extends Controller
{
    // 获取所有的角色列表
    public function get(){
        // 权限设置
        Authority::getInstance()->permitAll(true)->check(null);
        Access::Respond(1,RoleModel::getAll(),"角色列表获取成功");
    }
}