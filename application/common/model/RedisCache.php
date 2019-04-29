<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/8/8
 * Time: 22:05
 * Useage:常用工具
 * 参考网址: https://www.cnblogs.com/wenxinphp/p/6016449.html
 */
namespace app\common\model;
use think\facade\Config;
class RedisCache
{
    private static $instance = null;
    private $redis = null;

    public function __construct($options = [])
    {
        $this->redis = new \Redis();
        $this->redis->connect(Config::get("LINUX_HOST"), 6379, 2.5);
    }

    public static function getInstance () {
        if (self::$instance == null) {
            self::$instance = new RedisCache ();
            return self::$instance;
        } else {
            return self::$instance;
        }
    }

    /**
     * 返回redis常量的定义
     */
    public static function GetRedisKeys ($module, $index) {
        if (!defined("REDIS_TYPE") || !defined("REDIS_APP")) {
            // 必要常量需要定义
            Access::Respond (0, array(), 'redis常量未定义, 自动退出');
        }
        $obj = json_decode(REDIS_TYPE, true);
        if (isset ($obj[$module]) && isset($obj[$module][$index])) {
            // 如果已经定义键值
            $key = sprintf ("%s:%s:%s:%s", REDIS_APP, "shenzhen", $module, $obj[$module][$index]);
            return $key;
        } else {
            Access::Respond (0, array(), 'redis键值无法获取, 自动退出');
        }
    }

    /**
     * 设置字符串类型
     */
    public function set ($key, $name, $expire=NULL) {
        if ($expire == NULL) {
            $expire = $this->redis->options['expire'];
        }
        $this->handler->setex($key, $expire, $name);
    }

    /**
     * 获取字符串
     */
    public function get ($key, $default=true) {
        return $this->handler->get ($key);
    }

    /**
     * 删除字符串类型
     */
    public function rm($key){
        $this->handler->rm($key);
    }

    /*同时将多个 field-value (域-值)对设置到哈希表 key 中。
     * ** @param   string  $key
     * @param   array   $hashKeys key → value array
     * @return  bool
     * */
    public function hMset($key, $hashKeys)
    {
        return $this->redis->hMset($key,$hashKeys);
    }

    /*将哈希表 key 中的字段 field 的值设为 value 。
         *@param   string  $key
         * @param   string  $hashKey
         * @param   string  $value
     *  * @return  bool    TRUE if the field was set, FALSE if it was already present.
         * */
    public function hSet($key, $hashKey, $value)
    {
        return $this->redis->hSet($key, $hashKey, $value);
    }

    public function expire($key, $second=NULL) {
        if ($second == NULL) {
            $second = $this->redis->options['expire'];
        }
        $this->handler->expire ($key, $second);
    }

    public function sort ($key, $array) {
        return $this->handler->sort($key, $array);
    }

    /**
     * 删除哈希表中的某一个字段
     */
    public function hDel($key, $hashKey1)
    {
        $result = $this->redis->hDel($key, $hashKey1);
        return $result;
    }

    /**
     * 删除整个hash key
     */
    public function del ($key) {
        return $this->redis->del ($key);
    }

    /**
     * 判断哈希表内指定的字段是否存在
     */
    public function hExists($key, $hashKey)
    {
        $result = $this->redis->hExists($key, $hashKey);
        return $result;
    }

    /**
     * 获取存储在哈希表内指定字段的值
     */
    public function hGet($key, $hashKey)
    {
        $result = $this->redis->hGet($key, $hashKey);
        return $result;
    }

    /**
     * 获取存储在哈希表中所有字段的值
     */
    public function hGetall($key)
    {
        $result = $this->redis->hGetAll($key);
        return $result;
    }

    /**
     * 获取存储在哈希表中所有字段
     */
    public function hKeys($key)
    {
        $result = $this->redis->hKeys($key);
        return $result;
    }

    /**
     * 获取存储在哈希表中字段的数量
     */
    public function hLen($key)
    {
        $result = $this->redis->hLen($key);
        return $result;
    }

    public function lPush($key, $value) {
        $this->handler->lpush ($key, $value);
    }

    public function rPush ($key, $value) {

    }

    public function lInsert () {

    }

    public function lPushx ($key, $value) {

    }

    public function rPushx () {

    }

    public function lPop () {

    }

    public function rPop () {

    }

    public function lRem () {

    }

    public function lTrim () {

    }

    public function lSet () {

    }

    public function lIndex () {

    }

    public function lRange () {

    }

}