<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 22:52
 */

namespace app\admin\model;


use think\Model;
use think\Db;
use app\common\model\Common;
use app\common\model\Access;
class reply extends Model
{
    public static function in($list)
    {
        try {
            return Db::table("reply")->insertGetId($list);
        } catch (\Exception $e) {
           Access::Respond(0,array(),"评论失败");
        }
    }

    public static function del($replyIdList)
    {
        try {
            Db::table("reply")->whereIn('id', $replyIdList)->update(array('status' => 1));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function upd($replyId, $list)
    {
        try {
            Db::table("reply")->where('id', $replyId)->update($list);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function read($list)
    {
        $sql = "select id,postId,userId,content,praise,anonymous,create_time,update_time from reply where status=0";
        if (isset($list["id"])) {
            $sql .= " AND id=" . $list["id"];
        } else if (isset($list["idList"])) {
            $str = Common::generateSQL($list["idList"]);
            $sql .= " AND id In " . $str;
        } else if (isset($list["postId"])) {
            $sql .= " AND postId=" . $list["postId"];
        } else if (isset($list["userId"])) {
            $sql .= " AND userId=" . $list["userId"];
        }
        $result = Db::query($sql);
        return $result;
    }

    public static function getById($id)
    {
        try {
            $result = Db::table("reply")->where('id', $id)->where('status', 0)->findOrFail();
            return $result;
        } catch (\Exception $e) {
            Access::Respond(0, array(), "查询评论失败");
        }
    }

    // 点赞数递增
    public static function addParise($replyId){
        $data = self::getById($replyId);
        $data["praise"]++;
        return self::upd($data["id"],$data);
    }
    // 点赞数递减
    public static function delParise($replyId){
        $data = self::getById($replyId);
        $data["praise"]--;
        return self::upd($data["id"],$data);
    }
}