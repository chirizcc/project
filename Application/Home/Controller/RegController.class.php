<?php
namespace Home\Controller;
use Think\Controller;
class RegController extends HomeController
{
	public function index(){
		if(!empty(session('home_name')) ){
			$this->redirect('Index/index');
			exit;
		}
		$this->display();
	}

	public function code(){
    	$Verify = new \Think\Verify();
    	$Verify->fontSize = 30;
    	$Verify->length = 3;
		$Verify->imageW = 0;
		$Verify->imageH = 0;
    	$Verify->entry();
    }

    public function codetest(){
    	if (!IS_AJAX) {
            $this->error('快回去别闹',U('Index/index'));
            exit;
        }
        $val = I('post.val');

        if((new \Think\Verify())->check($val)){
        	$this->ajaxReturn(true);
        }else{
        	$this->ajaxReturn(false);  	
        }
    }

    public function usernametest(){
    	if (!IS_AJAX) {
            $this->error('别乱玩！',U('Index/index'));
            exit;
        }

        $val = I('post.val');
        $val = trim($val);

        //判断不能为空
        if($val == null){
        	$this->ajaxReturn('null');
        	exit;
        }

        /*//正则验证
        if(!preg_match("/^[a-zA-Z0-9]{2,9}$/", $val)){
		    $this->ajaxReturn('err');
		    exit;
		}*/

		//验证是否有重复
        $map['u_username'] = $val;
        if(empty(M('user')->where($map)->select())){
        	$this->ajaxReturn(true);
        }else{
        	$this->ajaxReturn(false);  	
        }
    }

    public function passwordtest(){
    	if (!IS_AJAX) {
            $this->error('滚吧',U('Index/index'));
            exit;
        }

        $val1 = I('post.val1');
        $val2 = I('post.val2');

        //判断不能为空
        if($val1 == null){
        	$this->ajaxReturn('null1');
        	exit;
        }
        if($val2 == null){
        	$this->ajaxReturn('null2');
        	exit;
        }

        //正则验证
        if(!preg_match('/((?=.*\d)(?=.*\D)|(?=.*[a-zA-Z])(?=.*[^a-zA-Z]))^.{8,16}$/', $val1)){
		    $this->ajaxReturn('err');
		    exit;
		}

        if($val1 == $val2){
        	$this->ajaxReturn('true');
        }else{
        	$this->ajaxReturn('false');  	
        }
    }

    public function reg(){
    	if (empty($_POST)) {
            $this->redirect('Home/Reg/index');
            exit;
        }

        //1.自己手动过滤POST数据
        //2.M('user')->data()  自动生成数据
        //3.推荐!
        // dump(I('post.'));
        $a = D('User')->create(I('post.'),1);
        // var_dump(D('User')->getError());exit;
        // dump(D('User')->fetchSql(true)->add());
        // die;
        //执行添加
        if (D('User')->add() > 0) {
           $this->success('恭喜您,添加成功!', U('Login/index'));
        } else {
           $this->error('添加失败....');
        }
    }

    public function sendEmail()
    {
        dump(I('post.'));
    }
}