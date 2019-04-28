<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 11:12
 */
namespace app\admin\controller;

use app\admin\model\Qiniu;
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
//       print_r(Qiniu::getInstance()->upload("https://www.coolholden.cn/static/uploads/39/20190427/3c6aafd6ac064eb4519269bdbe3cdc60.png"));
        $filePath = "https://www.coolholden.cn/static/uploads/39/20190427/3c6aafd6ac064eb4519269bdbe3cdc60.png";
        $file = fopen($filePath, 'rb');
        var_dump($file);
        $stat = fstat($file);
        print_r($stat);
    }

    public function index1(){
        print_r(Session::get());
        echo Common::getSession("name");
    }
}