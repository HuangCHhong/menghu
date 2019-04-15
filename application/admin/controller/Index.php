<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 11:12
 */
namespace app\admin\controller;

use think\Controller;
use think\facade\Config;
class Index extends Controller
{
    public function index(){
        echo Config::get("APPID");

    }
}