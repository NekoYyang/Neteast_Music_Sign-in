<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use lib\Music;
use think\Db;

class Api extends Controller
{
    public function _initialize()
    {
        $this->rootInfo = Db::name('root')->where('id',1)->find();
    }

    private static function isCookie($userId,$csrf,$musicu)
    {
        $cookie="__csrf={$csrf}; MUSIC_U={$musicu}";
        $music = new Music();
        $ret = $music->level($cookie);
        $musicInfo = json_decode($ret['body'],true);
        if(is_array($musicInfo) && array_key_exists("code",$musicInfo) && $musicInfo['code'] == 200){
            return true;
        }else{
            return false;
        }
    }

    public function music()
    {
        $key = Request::instance()->get('key');
        if(!isset($key) || $key != $this->rootInfo['key']){
            return 'key no';
        }else{
            $user = Db::name('user')
            ->where('cookieStatus',1)
            ->whereTime('vipDate','>=',date("Y-m-d"))
            ->where('executeDate',null)
            ->whereOr('executeDate','<>',date("Y-m-d"))
            ->find();
            if(self::isCookie($user['userId'],$user['csrf'],$user['musicu'])){
                $cookie="__csrf={$user['csrf']}; MUSIC_U={$user['musicu']}";
                $music = new Music();
                $music->daka_new($cookie);
                $music->sign($cookie);
                Db::name('user')->where('uid',$user['uid'])->update(['executeDate'=>date("Y-m-d")]);
            }else{
                Db::name('user')->where('uid',$user['uid'])->update(['cookieStatus'=>0]);
            }
            return 'ok!';
        }
    }

    public function upCookie()
    {
        $key = Request::instance()->get('key');
        if(!isset($key) || $key != $this->rootInfo['key']){
            return 'key no';
        }else{
            $userInfo = Db::name('user')->where('cookieStatus',0)->limit(100)->select();
            foreach($userInfo as $user){
                $music = new Music();
                $musicLogin = $music->login($user['uin'],$user['pwd']);
                $musicHeader = $musicLogin['header'];
                $musicBody = json_decode($musicLogin['body'],true);
                if(!is_array($musicBody) || !array_key_exists("code",$musicBody) || $musicBody['code'] == 400){
                }else if($musicBody['code'] == 200){
                    preg_match_all('/Set-Cookie: MUSIC_U=(.*?)\;/', $musicHeader, $musicu);
                    preg_match_all('/Set-Cookie: __csrf=(.*?)\;/', $musicHeader, $csrf);
                    Db::name('user')->where('uin',$user['uin'])->update(['musicu'=>$musicu[1][0],'csrf'=>$csrf[1][0],'cookieStatus'=>1]);
                }
            }
            return 'ok!';
        }
    }
}