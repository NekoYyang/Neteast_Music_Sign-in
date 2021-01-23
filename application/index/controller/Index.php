<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use lib\Music;
use lib\Mcrypt;
use think\Db;
use think\Cookie;

class Index extends Controller
{
    public function _initialize()
    {
        $this->rootInfo = Db::name('root')->where('id',1)->find();
        $this->assign('rootInfo',$this->rootInfo);
    }

    public function index()
    {
        Cookie::has('token')?$this->assign('isLogin',true):$this->assign('isLogin',false);
        return $this->fetch();
    }

    public function login()
    {
        if(Request::instance()->isAjax()){
            $data = Request::instance()->post();
            $music = new Music();
            $musicLogin = $music->login($data['uin'],$data['pwd']);
            $musicHeader = $musicLogin['header'];
            $musicBody = json_decode($musicLogin['body'],true);
            if(!captcha_check($data['captcha'])){
                return json(["status"=>0,"msg"=>"验证码错误！"]);
            }else if(!is_array($musicBody) || !array_key_exists("code",$musicBody) || $musicBody['code'] == 400){
                return json(["status"=>-1,"msg"=>"登陆失败：未知异常！"]);
            }else if($musicBody['code'] == 200){
                preg_match_all('/Set-Cookie: MUSIC_U=(.*?)\;/', $musicHeader, $musicu);
                preg_match_all('/Set-Cookie: __csrf=(.*?)\;/', $musicHeader, $csrf);
                if(Db::name('user')->where('uin',$data['uin'])->find()){
                    Db::name('user')->where('uin',$data['uin'])->update(['pwd'=>$data['pwd'],'musicu'=>$musicu[1][0],'csrf'=>$csrf[1][0],'cookieStatus'=>1]);
                }else{
                    Db::name('user')->insert(['uin'=>$data['uin'],'pwd'=>$data['pwd'],'userId'=>$musicBody['account']['id'],'musicu'=>$musicu[1][0],'csrf'=>$csrf[1][0],'addDate'=>date("Y-m-d H:i:s")]);
                }
                cookie::set('token',Mcrypt::encode($data['uin'].'|'.$data['pwd']));
                return json(["status"=>1,"msg"=>"登陆成功：".$musicBody['profile']['nickname']]);
            }else{
                return json(["status"=>0,"msg"=>$musicBody['message']]);
            }
        }else{
            return $this->fetch();
        }
    }
}
