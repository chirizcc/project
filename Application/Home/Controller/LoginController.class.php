<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends HomeController
{
	public function index(){
		if (empty($_POST)) {
	        $this->redirect('Home/Index/index');
	        exit;
	    }

	    $username = $_POST['username'];
	    $username = trim($username);
	    $password = $_POST['password'];
	    $password = trim($password);
	    // $password = md5($password);
	    // var_dump($password);exit;

	    $map = [];
	    $map['u_username'] = $username;
	    $map['u_password'] = $password;
		$user = M('user')->where($map)->find();
   		//执行添加

        if (!empty($user)) {
            session('name',$username);
            session('id',$user['u_id']);
            $this->success('恭喜您,登录成功!', U('Index/index'));
    		// $this->redirect('Index/index');
        } else {
           $this->error('账号或密码错误....');
    		// $this->redirect('Login/index', array('tip' => '账号或密码错误'));
        }
	}

}