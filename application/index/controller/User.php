<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use lib\Music;
use lib\Mcrypt;
use think\Db;
use think\Cookie;

class User extends Controller
{
    public function _initialize()
    {
        $this->userInfo = $this->isLogin();
        $this->musicInfo = $this->isCookie($this->userInfo['userId'],$this->userInfo['csrf'],$this->userInfo['musicu']);
        $this->rootInfo = Db::name('root')->where('id',1)->find();
        $this->assign('rootInfo',$this->rootInfo);
    }

    public function index()
    {
        $this->assign('musicInfo',$this->musicInfo);
        $this->assign('userInfo',$this->userInfo);
        return $this->fetch();
    }

    public function  openVip()
    {
        $km = Request::instance()->post('km');
        if($kmInfo = Db::name('km')->where('km',$km)->where('uin',null)->find()){
            $vipDate = self::vipDate($this->userInfo['vipDate'],$kmInfo['days']);
            Db::name('km')->where('kid',$kmInfo['kid'])->update(['uin'=>$this->userInfo['uin']]);
            if(Db::name('user')->where('uid',$this->userInfo['uid'])->update(['vipDate'=>$vipDate])){
                return json(["status"=>1,"msg"=>"成功充值<font color='red'><b>{$kmInfo['days']}</b></font>天VIP"]);
            }else{
                return json(["status"=>-1,"msg"=>"充值失败：请联系管理员"]);
            }
        }else{
            return json(["status"=>0,"msg"=>"卡密不存在或已使用"]);
        }
    }

    private static function vipDate($date,$days)
    {
        $vipDate = '';
        if($date == null || $date<date("Y-m-d")){
            $vipDate = date("Y-m-d",strtotime("+$days day"));
        }else{
            $vipDate = date("Y-m-d",strtotime("$date +$days day"));
        }
        return $vipDate;
    }

    private function isLogin()
    {
        if(cookie::has('token')){
            $token = cookie::get('token');
            $token = Mcrypt::decode($token);
            $login = explode('|',$token);
            $login = Db::name('user')->where('uin',$login[0])->where('pwd',$login[1])->find();
            if(!$login){
                return $this->error('不会真有人没登陆就想进用户中心吧？？？','/index/index/login.html');
            }else{
                return $login;
            }
        }else{
            return $this->error('不会真有人没登陆就想进用户中心吧？？？','/index/index/login.html');
        }
    }

    public function daka()
    {
        if($this->userInfo['vipDate'] == null || $this->userInfo['vipDate']<date("Y-m-d")){
            return json(["status"=>-1,"msg"=>"您不是VIP用户或已到期"]);
        }else{
            $music = new Music();
            $cookie="__csrf={$this->userInfo['csrf']}; MUSIC_U={$this->userInfo['musicu']}";
            $music->daka_new($cookie);
            $music->sign($cookie);
            return json(["status"=>1,"msg"=>"执行完毕"]);
        }
    }

    public function logOut()
    {
        cookie::delete('token');
        return json(["status"=>1,"msg"=>"退出成功"]);
    }

    private function isCookie($userId,$csrf,$musicu)
    {
        $cookie="__csrf={$csrf}; MUSIC_U={$musicu}";
        $music = new Music();
        $ret = $music->detail($userId,$cookie);
        $musicInfo = json_decode($ret['body'],true);
        $ret2 = $music->level($cookie);
        $musicInfo2 = json_decode($ret2['body'],true);
        if(is_array($musicInfo2) && array_key_exists("code",$musicInfo2) && $musicInfo2['code'] == 200){
            $musicInfo['nextLoginCount'] = $musicInfo2['data']['nextLoginCount'];
            $musicInfo['nowLoginCount'] = $musicInfo2['data']['nowLoginCount'];
            $musicInfo['nextPlayCount'] = $musicInfo2['data']['nextPlayCount'];
            $musicInfo['nowPlayCount'] = $musicInfo2['data']['nowPlayCount'];
            return $musicInfo;
        }else{
            return $this->error('登陆失效','/index/index/login.html');
        }
    }
}