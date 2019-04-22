<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 11:12
 */
namespace app\admin\controller;

use app\common\model\Access;
use think\Controller;
use think\facade\Config;
use app\common\model\Common;
class Index extends Controller
{
    public function index(){
        Common::setSession("name","holden",false);
        Access::Respond(0,array(),"success");
    }
    public function index1(){
        echo Common::getSession("name");
    }
}