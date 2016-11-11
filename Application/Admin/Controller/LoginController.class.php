<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller
{
	public function index(){	
		$this->display('Login/index');
	}

	public function code(){
    	$Verify = new \Think\Verify();
    	$Verify->fontSize = 30;
    	$Verify->length = 4;
		$Verify->imageW = 0;
		$Verify->imageH = 0;
    	$Verify->entry();
    }

    public function yz(){
    	if (!IS_AJAX) {
            $this->error('您老迷路了吧? 赶紧回首页吧!',U('Index/index'));
            exit;
        }
        $val = I('post.val');

        if((new \Think\Verify())->check($val)){
        	$this->ajaxReturn('true');
        }else{
        	$this->ajaxReturn('false');  	
        }
    }

    public function login(){
		if (empty($_POST)) {
	        $this->redirect('Admin/Login/index');
	        exit;
	    }

	    $username = $_POST['username'];
	    $username = trim($username);
	    $password = $_POST['password'];
	    $password = trim($password);

	    $map = [];
	    $map['u_username'] = $username;
	    $map['u_password'] = $password;
		$user = M('user')->where($map)->find();
	   		//执行添加

	        if (!empty($user)) {
	            session('name',$username);
	            session('id',$user['u_id']);
				session('type',$user['u_istype']);
	            // $this->success('恭喜您,登录成功!', U('Index/index'));
        		$this->redirect('Index/index');
	        } else {
	           // $this->error('登录失败....');
        		$this->redirect('Login/index', array('tip' => '账号或密码错误'));


	        }

    }

    public function logout(){
    	session(null);
        $this->redirect('Login/index');
    	
    }




}