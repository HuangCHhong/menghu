<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/25
 * Time: 15:55
 */

namespace app\admin\model;


use think\Model;
use app\common\model\Access;
use think\facade\App;
use app\admin\model\File;
class Url extends Model
{
    /**
     * 文件上传
     * @function: 保存文件详细信息到数据库中并返回文件ID
     */
    public static function uploadHandle ($userid, $uploadname, $filetype = "icon") {
        if (!isset($uploadname)) {
            Access::Respond (false, array(), "上传出错, 没有选择文件");
        }
        $serverHost = $_SERVER['HTTP_HOST'];
        $absoluteRoot = '..';
        $relateDir = 'static/uploads/'.$userid; // 相对路径
        $absolutedir = $absoluteRoot.'/'.$relateDir;                                // 绝对路径
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file($uploadname);
        if ($file == null) {
            Access::Respond (false, array(), "上传出错不是form方式提交的文件");
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(Access::getFileExtendConf ($filetype))->move($absolutedir);

        if($info){
            // 成功上传后 获取上传信息
            $extensionName = $info->getExtension();                  // 后缀名
            $relatePath = $relateDir.'/'.$info->getSaveName ();       // 全局路径
            $absolutePath = "http://".$serverHost.'/'.$relatePath;
            $filesize = $info->getSize ();

            $ret['host'] = $serverHost;                         // 主机名
            $ret['routedir'] = $absoluteRoot;                      // 根路由目录
            $ret['routedir'] = null;
            $ret['absolutePath'] = $absolutePath;               // 最终路径
            $ret['relatePath'] = $relatePath;                   // 相对路径
            $ret['extendname'] = $extensionName;                // 拓展名
            $ret['filesize'] = $filesize;                       // 文件大小

            /** 保存此记录到数据库中 */
            $linkid = File::in ($ret);
            $ret['fileId'] = $linkid;

            // 关闭$file!!!!!!!!!!!!!!!!!!!!
            $info = null;   // 否则直接使用unlink会提示Permission denied.

            return $ret;
        }else{
            // 上传失败获取错误信息
            Access::Respond (0, array(), $file->getError());
        }
    }
}