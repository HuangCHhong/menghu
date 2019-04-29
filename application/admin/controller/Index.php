<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 11:12
 */
namespace app\admin\controller;

use app\admin\model\File;
use app\admin\model\Qiniu;
use app\common\model\Access;
use app\common\model\RedisCache;
use think\facade\App;
use think\Controller;
use think\facade\Config;
use app\common\model\Common;
use think\facade\Session;
use app\admin\model\Url;

class Index extends Controller
{

    public function index(){
        $redis = new \Redis();
        $result = $redis->connect(Config::get("LINUX_HOST"), 6379, 2.5);
        $result = $redis->auth('12346578');
        $result = $redis->set('cs','134984848949');
        $result = $redis->get('cs');
        echo $result;
    }

    public function index1(){
        print_r(Session::get());
        echo Common::getSession("name");
    }
}