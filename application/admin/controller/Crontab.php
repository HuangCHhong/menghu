<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/28
 * Time: 14:31
 */

namespace app\admin\controller;


use app\common\model\Access;
use think\Controller;
use app\admin\model\File;
use app\common\model\Qiniu;
class Crontab extends Controller
{
    //定时文件备份
    public function backup(){
        $prefix = "/menghu/";
        $qiniu_prefix = "http://pqngqil0k.bkt.clouddn.com/";
        $fileInfos = File::getFile();
        $successData = array(array());
        //备份
        foreach ($fileInfos as $fileInfo){
            $file = $prefix.$fileInfo["relatePath"];
            $ret = Qiniu::getInstance()->upload($file);
            $successData[] = array(
                "id"=>$fileInfo["id"],
                'backupAddr'=>$qiniu_prefix.$ret["key"],
                'isBackup'=>1
            );
        }
        //批量更新数据库
        File::updateFilePath($successData);
        Access::Respond(1,array(),"备份成功");
    }
}