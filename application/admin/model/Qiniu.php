<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/28
 * Time: 10:31
 */

namespace app\admin\model;

use Qiniu\Storage\UploadManager;
use Qiniu\Auth;
use \Config;
class Qiniu
{

    private static $instance = null;
    private $token = null;
    public function __construct () {
        // 上传工具
        $this->uploadMgr = new UploadManager ();

        // 设置密钥公钥
        $auth = new Auth(Config::get("Qiniu_AccessKey"), Config::get("Qiniu_SecretKey"));

        // 设置空间
        $this->token = $auth->uploadToken(Config::get("Qiniu_BucketName"));
    }

    public static function getInstance () {
        if (is_null(Qiniu::$instance)) {
            self::$instance = new Qiniu ();
            return self::$instance;
        } else {
            return self::$instance;
        }
    }

    /**
     * 上传文件
     */
    public function upload ($filename) {
        //将字符串切割成数组
        $fileArrs = explode('/', $filename);
        $remotefilename = "";
        if (count ($fileArrs) > 1) {
            $remotefilename = $fileArrs[count($fileArrs)-1];
        } else {
            $remotefilename = $filename;
        }

        list($ret, $err) =  $this->uploadMgr->putFile ($this->token, $remotefilename, $filename);
        if ($err !== null) {
            $ret['ifsuccess'] = false;
            $ret['data'] = $err;
        } else {
            $ret['ifsuccess'] = true;
            $ret['data'] = $ret;
        }
        return $ret;
    }
}