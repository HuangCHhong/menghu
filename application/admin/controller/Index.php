<?php
/**
 * Created by PhpStorm.
 * User: holden
 * Date: 2019/4/7
 * Time: 11:12
 */
namespace app\admin\controller;

use think\Controller;
use think\facade;
class Index extends Controller
{
    public function index(){
       $arr = array(1,2,3);
       foreach ($arr as &$data){
           $data = 1;
       }
       print_r($arr);
    }
}