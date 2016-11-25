<?php
namespace Home\Controller;
use Think\Controller;

//前台登录控制器
class LoginController extends HomeController
{
	//首页方法
	public function index(){
		$this->display();
	}

	//登录验证方法
	public function yanzheng(){
		if (empty($_POST)) {
	        $this->redirect('Home/Index/index');
	        exit;
	    }

	    $username = $_POST['username'];
	    $username = trim($username);
	    $password = $_POST['password'];
	    $password = trim($password);
	    $password = md5($password);

	    $map = [];
	    $map['u_username'] = $username;
	    $map['u_password'] = $password;
		$user = M('user')->where($map)->find();

        if (!empty($user)) {
        	if($user['u_istype'] == '0'){
        		$this->error('您的账号被禁用 请联系管理员');
        	}
            session('home_name',$username);
            session('home_id',$user['u_id']);
            session('home_type',$user['u_istype']);
            $this->success('恭喜您,登录成功!', U('Index/index'));
        } else {
           $this->error('账号或密码错误....');
        }
	}

	public function logout(){
		session('home_name',null);
        session('home_id',null);
        session('home_type',null);
        $this->redirect('Index/index');
	}

}