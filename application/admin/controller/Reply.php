<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/8
 * Time: 18:35
 */

namespace app\admin\controller;


use app\admin\model\Gateway;
use app\common\model\Elastic;
use think\Controller;
use app\common\model\Authority;
use app\common\model\Access;
use app\admin\model\reply as replyModel;
use app\admin\model\User as UserModel;
use app\admin\model\Post as PostModel;
use app\admin\model\weight as weightModel;
use \Config;

class Reply extends Controller
{
    // 查看回复详情
    public function getReplyInfo(){
        $randName = ["凯","宫本武藏","苍老师","狄仁杰","路飞","魏锁辉"];
        $randUrl = [
            "https://image.baidu.com/search/detail?ct=503316480&z=0&ipn=false&word=%E6%90%9E%E7%AC%91%E5%A4%B4%E5%83%8F&step_word=&hs=0&pn=0&spn=0&di=1870&pi=0&rn=1&tn=baiduimagedetail&is=0%2C0&istype=0&ie=utf-8&oe=utf-8&in=&cl=2&lm=-1&st=undefined&cs=3134952146%2C3980288478&os=2227331366%2C1923920119&simid=4226348434%2C526076998&adpicid=0&lpn=0&ln=3458&fr=&fmq=1556177560971_R&fm=&ic=undefined&s=undefined&hd=undefined&latest=undefined&copyright=undefined&se=&sme=&tab=0&width=undefined&height=undefined&face=undefined&ist=&jit=&cg=head&bdtype=0&oriquery=&objurl=http%3A%2F%2Fimg3.duitang.com%2Fuploads%2Fitem%2F201506%2F17%2F20150617112607_3sMvK.jpeg&fromurl=ippr_z2C%24qAzdH3FAzdH3Fooo_z%26e3B17tpwg2_z%26e3Bv54AzdH3Fks52AzdH3F%3Ft1%3Dc0a0dda90&gsm=0&rpstart=0&rpnum=0&islist=&querylist=&force=undefined",
            "https://image.baidu.com/search/detail?ct=503316480&z=0&ipn=false&word=%E6%90%9E%E7%AC%91%E5%A4%B4%E5%83%8F&step_word=&hs=0&pn=1&spn=0&di=4290&pi=0&rn=1&tn=baiduimagedetail&is=0%2C0&istype=0&ie=utf-8&oe=utf-8&in=&cl=2&lm=-1&st=undefined&cs=149216237%2C2944314823&os=2070085497%2C1740539738&simid=4154990393%2C691789569&adpicid=0&lpn=0&ln=3458&fr=&fmq=1556177560971_R&fm=&ic=undefined&s=undefined&hd=undefined&latest=undefined&copyright=undefined&se=&sme=&tab=0&width=undefined&height=undefined&face=undefined&ist=&jit=&cg=head&bdtype=0&oriquery=&objurl=http%3A%2F%2Fimg5.duitang.com%2Fuploads%2Fitem%2F201502%2F26%2F20150226211219_k2BkZ.jpeg&fromurl=ippr_z2C%24qAzdH3FAzdH3Fooo_z%26e3B17tpwg2_z%26e3Bv54AzdH3Frj5rsjAzdH3F4ks52AzdH3Fnn908m0mcAzdH3F1jpwtsAzdH3F&gsm=0&rpstart=0&rpnum=0&islist=&querylist=&force=undefined",
            "https://image.baidu.com/search/detail?ct=503316480&z=0&ipn=d&word=%E8%94%A1%E5%BE%90%E5%9D%A4&step_word=&hs=0&pn=10&spn=0&di=179630&pi=0&rn=1&tn=baiduimagedetail&is=0%2C0&istype=2&ie=utf-8&oe=utf-8&in=&cl=2&lm=-1&st=-1&cs=240287866%2C1229624882&os=367412209%2C537914622&simid=4132656203%2C700100089&adpicid=0&lpn=0&ln=1913&fr=&fmq=1556177613164_R&fm=result&ic=0&s=undefined&hd=0&latest=0&copyright=0&se=&sme=&tab=0&width=&height=&face=undefined&ist=&jit=&cg=&bdtype=0&oriquery=&objurl=http%3A%2F%2Fb-ssl.duitang.com%2Fuploads%2Fitem%2F201805%2F26%2F20180526170153_x8XeX.jpeg&fromurl=ippr_z2C%24qAzdH3FAzdH3Fooo_z%26e3B17tpwg2_z%26e3Bv54AzdH3Fks52AzdH3F%3Ft1%3Dlnn989808&gsm=0&rpstart=0&rpnum=0&islist=&querylist=&force=undefined",
        ];
        // 权限设置
        Authority::getInstance()->permitAll(true)->check(null);
        // 必选参数
        Access::MustParamDetect('postId');
        // 可选参数
        $param = array('id','idList','postId','userId','firstTime','endTime');
        $paramList = Access::OptionalParamOfList($param);
        // 获取评论数据
        $data = replyModel::read($paramList);
        // 获取可信度
        foreach ($data as &$replyInfo){
            // 获取帖子信息
            $post = PostModel::getById($replyInfo["postId"]);
            // 获取回帖人详情
            $user = UserModel::getByUserId( $replyInfo["userId"]);
            // 获取话题相关系数
            $weight = weightModel::getById($post["typeId"],$user["roleId"]);
            $weightNum = 1;
            if(!empty($weight)){
                $weightNum = $weight["weight"];
            }
            // 权威值计算公式：weight*praiseCount
            $replyInfo["score"] = $weightNum * $user["praiseCount"];
            //获取用户信息
            if($replyInfo["anonymous"]){
                $replyInfo["nickName"] = $user["nickName"];
                $replyInfo["avatarUrl"] = $user["avatarUrl"];
            }else{
                $replyInfo["nickName"] = $randName[mt_rand(0,count($randName)-1)];
                $replyInfo["avatarUrl"] = $randUrl[mt_rand(0,count($randUrl)-1)];
            }
        }
        Access::Respond(1,$data,"获取帖子回复成功");
    }

    // 删除帖子回复
    public function delReply(){
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("idLIst");
        Access::MustParamDetectOfRawData($mustParam,$data);
        // 获取帖子所有者
        $userList = array();
        $replyIdList = array();
        $replyInfos = replyModel::read($data);
        foreach ($replyInfos as $replyInfo){
            array_push($userList,$replyInfo["userId"]);
            array_push($postIdList,$replyInfo["id"]);
        }
        $userList = array_unique($userList);
        $replyIdList = array_unique($replyIdList);
        // 权限验证
        if(count($userList) > 1){
            // 涉及到多个用户的帖子，则只有管理员才有权限操作
            Authority::getInstance()->permit(array(Config::get("ADMIN")))->check(null);
        }
        Authority::getInstance()->permit(array(Config::get("ADMIN"),Config::get("ORDINARY")))->check($userList[0]);
        // 帖子删除
        $ok = replyModel::del($replyIdList);
        if(!$ok){
            Access::Respond(0,array(),"评论删除失败");
        }
        Access::Respond(1,array(),"评论删除成功");
    }

    public function addReply(){
        // 权限验证
        $userId = null;
        $flag = null;
        Authority::getInstance()->permit(array(Config::get("ORDINARY")))->check(null)->loadAccount($flag,$userId);
        // 解析json
        $data = Access::deljson_arr(file_get_contents("php://input"));
        // 必选参数
        $mustParam = array("content","postId");
        Access::MustParamDetectOfRawData($mustParam,$data);
        // 存储到DB
        $data["userId"] = $userId;
        $id = replyModel::in($data);
        // 存储到ES
        $reply = replyModel::getById($id);
        Elastic::getInstance()->addDoc($reply["id"],"reply",$reply);
        // 评论成功后发送给发帖人
        $post = PostModel::getById($data["postId"]);
        Gateway::sendToUid($post["userId"],"收到一条评论，可立即查看");
        Access::Respond(1,array(),"评论成功");
    }
}