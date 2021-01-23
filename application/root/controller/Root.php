<?php
namespace app\root\controller;

use think\Controller;
use think\Request;
use lib\Mcrypt;
use think\Db;
use think\Cookie;
use think\Paginator;

class Root extends Controller
{
    public function _initialize()
    {
        $this->rootInfo = $this->isLogin();
        $this->assign('rootInfo',$this->rootInfo);
    }

    public function index()
    {
        $num = [
            'user'=>Db::name('user')->count(),
            'addUser'=>Db::name('user')->whereTime('addDate','today')->count(),
            'km'=>Db::name('km')->count()
        ];
        $this->assign('num',$num);
        return $this->fetch();
    }

    public function user()
    {
        $list = Db::name('user')->paginate(10);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function km()
    {
        $list = Db::name('km')->paginate(10);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function createKm()
    {
        $data = Request::instance()->post();
        if(!is_numeric($data['days']) || strpos($data['days'],".") || $data['days']<=0){
            return json(["status"=>0,"msg"=>"天数不正确"]);
        }else if(!is_numeric($data['num']) || strpos($data['num'],".") || $data['num']<=0){
            return json(["status"=>0,"msg"=>"生成数量不正确"]);
        }else if($data['num']>100){
            return json(["status"=>0,"msg"=>"一次最多生成100张"]);
        }else{
            $kmlist = '';
            for($i=1;$i<=$data['num'];$i++){
                $km = self::randKm();
                Db::name('km')->insert(['km'=>$km,'days'=>$data['days'],'addDate'=>date('Y-m-d H:i:s')]);
                $kmlist .= $km.'<br>';
            }
            return json(["status"=>1,"msg"=>"生成成功","data"=>["km"=>$kmlist]]);
        }
    }

    public function kmDel()
    {
        $kid = Request::instance()->post('kid');
        if(Db::name('km')->where('kid',$kid)->delete()){
            return json(["status"=>1,"msg"=>"删除成功"]);
        }else{
            return json(["status"=>0,"msg"=>"删除失败"]);
        }
    }

    public function userDel()
    {
        $uid = Request::instance()->post('uid');
        if(Db::name('user')->where('uid',$uid)->delete()){
            return json(["status"=>1,"msg"=>"删除成功"]);
        }else{
            return json(["status"=>0,"msg"=>"删除失败"]);
        }
    }

    private static function randKm()
    {
        $str = '';
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $char_len = strlen($chars);
        for($i=0;$i<12;$i++){
            $loop = mt_rand(0, ($char_len-1));
            $str .= $chars[$loop];
        }
        return md5($str.time()).date("Ymd");
    }

    public function rootEdit()
    {
        $data = Request::instance()->post();
        if(Db::name('root')->where('id',1)->update(['webName'=>$data['webName'],'key'=>$data['key'],'notice'=>$data['notice']])){
            return json(["status"=>1,"msg"=>"修改成功"]);
        }else{
            return json(["status"=>0,"msg"=>"修改失败"]);
        }
    }

    public function pwdEdit()
    {
        $password = Request::instance()->post('pwd');
        if(Db::name('root')->where('id',1)->update(['password'=>$password])){
            return json(["status"=>1,"msg"=>"修改成功"]);
        }else{
            return json(["status"=>0,"msg"=>"修改失败"]);
        }
    }

    private function isLogin()
    {
        if(cookie::has('root_token')){
            $token = cookie::get('root_token');
            $token = Mcrypt::decode($token);
            $login = explode('|',$token);
            $login = Db::name('root')->where('username',$login[0])->where('password',$login[1])->find();
            if(!$login){
                return $this->error('不会真有人没登陆就想进后台吧？？？','/root/index/index.html');
            }else{
                return $login;
            }
        }else{
            return $this->error('不会真有人没登陆就想进后台吧？？？','/root/index/index.html');
        }
    }

    public function logOut()
    {
        cookie::delete('root_token');
        return json(["status"=>1,"msg"=>"退出成功"]);
    }
}