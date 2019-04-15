<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 9:28
 */

namespace app\common\model;


use app\admin\model\BlackList;
use app\admin\model\User;
use think\facade\Config;

class Authority
{
    private static $instance = null;

    private $userid = null;                 // 用户ID
    private $flag = null;                   // 登陆类型
    private $openId = null;
    private $permitallenable = false;              // 所有已登陆用户均可访问
    private $permitList = null;             // 允许的登陆组

    public function __construct () {
        $this->instance = null;
        $this->userid = null;
        $this->flag = null;
        $this->permitallenable = false;
        $this->permitList = array();             // 允许的登陆组
    }

    public static function getInstance () {
        if (is_null(Authority::$instance)) {
            self::$instance = new Authority ();
            return self::$instance;
        } else {
            return self::$instance;
        }
    }

    /**
     * 对所有登陆组公开
     */
    public function permitAll ($enable) {
        $this->permitallenable = $enable;
        return self::$instance;
    }

    /**
     * 配置接口访问权限
     */
    public function permit ($flagArr) {
        $this->permitList = $flagArr;
        return self::$instance;
    }

    /**
     * 验证权限
     */
    public function check ($userId) {

        // 获取用户登陆信息
        $this->_loadSession ();

        // 验证用户全局访问权限
        $this->_userGlobalFirewall ();

        // 验证接口访问权限
        $this->_flagApiFirewall ($userId);

        return self::$instance;
    }

    /**
     * 获取登陆的session
     */
    private function _loadSession () {
        $this->openId = Common::getSession (Config::get("SESSION_OPENID"));
        if(!Common::hasSesssion(Config::get("SESSION_FLAG"))){
            //从DB中获取此人的完整信息
            $userInfo = User::read($this->userid);
            if(!$userInfo){
                Access::Respond(0,array(),"拉取用户信息失败");
            }
            Common::setSession(Config::get("SESSION_FLAG"),$userInfo["roleId"]);
            Common::setSession(Config::get("SESSION_USERID"),$userInfo["id"]);
        }
        $this->flag = Common::getSession (Config::get("SESSION_FLAG"));
        $this->userid = Common::getSession(Config::get("SESSION_USERID"));
        //查找黑名单
        $data = BlackList::getAll();
        foreach ($data as $user){
            if($user["userId"] == $this->userid){
                Access::Respond (0, array(), '您已被加入黑名单，无权限登录');
            }
        }
        if (isset($this->userid) && isset($this->flag)
            && $this->userid != "" && $this->flag != "") {
        } else {
            Access::Respond (0, array(), '请先进行登录');
        }
    }

    /**
     * 用户过滤
     */
    private function _userGlobalFirewall () {
    }

    /**
     * 用户权限接口过滤
    */
    private function _flagApiFirewall ($userId) {
        if ($this->permitallenable){
            if(empty($userId)){
                return true;
            }
            if ($this->flag == Config::get("ADMIN")){
                return true;
            }else{
                if ($this->userid == $userId){
                    return true;
                }else{
                    Access::Respond (0, array(), '此账户没有访问权限');
                }
            }
        }
        foreach ($this->permitList as $permit){
            if ($permit == Config::get("ORDINARY") && $this->flag == Config::get("ORDINARY")){
                if(empty($userId)){
                    return true;
                }
                if ($this->userid == $userId){
                    return true;
                }else{
                    Access::Respond (0, array(), '此账户没有访问权限');
                }
            }
            if ($permit == Config::get("ADMIN") && $this->flag == Config::get("ADMIN")){
                return true;
            }
        }

        Access::Respond (0, array(), '此账户没有访问权限');
    }

    /**
     * 获取登陆账号
     */
    public function loadAccount (&$flag, &$userid) {
        $flag = $this->flag;
        $userid = $this->userid;
        return self::$instance;
    }
}