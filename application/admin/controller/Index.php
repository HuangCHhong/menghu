<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 11:12
 */
namespace app\admin\controller;

use app\common\model\Access;
use think\facade\App;
use think\Controller;
use think\facade\Config;
use app\common\model\Common;
use think\facade\Session;
use app\admin\model\Url;

class Index extends Controller
{
    public function index(){
//        echo App::getAppPath();
        echo json_encode(Url::uploadHandle(1,"upload"));
    }
    public function index1(){
        print_r(Session::get());
        echo Common::getSession("name");
    }
}