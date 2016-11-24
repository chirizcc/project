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
	    $map['u_password'] = md5($password);
		$user = M('user')->where($map)->find();

		if (!empty($user)) {

            //获取该用户的权限
            $list = M('user_role')->table('zd_user_role ur,zd_role r,zd_role_node rn,zd_node n')->where('ur.ur_rid = r.r_id and r.r_id = rn.rn_rid and rn.rn_nid = n.n_id and ur.ur_uid = '.$user['u_id'])->field('n.n_controller controller,n.n_action action')->select();

            if(empty($list)) {
                $this->redirect('Login/index', array('tip' => '您没有管理员权限！！'));
                die;
            }

            // 将控制器的首字母转换成大写
            foreach ($list as $k => $v) {
                $list[$k]['controller'] = ucfirst($v['controller']);
            }

            // 重组数组
            $newList = [];
            foreach ($list as $k => $v) {
                $newList[$v['controller']][] = $v['action'];
            }

            // 将登录用户信息写入session中
            session('name',$username);
            session('id',$user['u_id']);
            // 权限信息存入到session中
            session('nodeList',$newList);

			$this->redirect('Index/index');
		} else {
			$this->redirect('Login/index', array('tip' => '账号或密码错误'));
		}

    }

    public function logout(){
        session('name',null);
        session('id',null);
        $this->redirect('Login/index');
    }

}