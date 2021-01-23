<?php
namespace app\root\controller;

use think\Controller;
use think\Request;
use lib\Mcrypt;
use think\Db;
use think\Cookie;

class Index extends Controller
{
    public function index()
    {
        if(Request::instance()->isAjax()){
            $data = Request::instance()->post();
            if(Db::name('root')->where('username',$data['username'])->where('password',$data['password'])->find()){
                $token = Mcrypt::encode($data['username'].'|'.$data['password']);
                cookie::set('root_token',$token);
                return json(["status"=>1,"msg"=>"欢迎主人回家！"]);
            }else{
                return json(["status"=>0,"msg"=>"爪巴！！！"]);
            }
        }else{
            return $this->fetch();
        }
    }
}