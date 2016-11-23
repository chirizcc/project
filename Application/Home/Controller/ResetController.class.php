<?php
namespace Home\Controller;
use Think\Controller;

class ResetController extends JudgeController
{
	public function index()
	{	
		$data=M('detail')->where('det_uid ='.session('home_id'))->field('det_tel,det_email')->select();
		$this->assign('data',$data[0]);
		$this->display();
	}

	public function code()
	{
    	$Verify = new \Think\Verify();
    	$Verify->fontSize = 30;
    	$Verify->length = 3;
		$Verify->imageW = 0;
		$Verify->imageH = 0;
    	$Verify->entry();
    }

    public function codetest()
    {
    	if (!IS_AJAX) {
            $this->error('快回去别闹',U('index'));
            exit;
        }
        $val = I('post.val');

        if((new \Think\Verify())->check($val)){
        	$this->ajaxReturn(true);
        }else{
        	$this->ajaxReturn(false);  	
        }
    }

    public function res(){
    	if (empty($_POST)) {
            $this->redirect('Home/Reset/index');
            exit;
        }         
        //执行修改
        $maps = [];      
        $maps['u_password'] = md5($_POST['u_password']);       
        if(M('user')->where('u_id ='.session('home_id'))->save($maps)){

            $this->success('恭喜您,修改成功!');
        }else{
            $this->error('修改失败....');
        }

    }

    public function sendSms()
    {
        if (!IS_AJAX) {
            $this->error('滚吧',U('Reset/index'));
            exit;
        }

        //正则验证 
        $tel = I('post.val');
        if(!preg_match('/^1[3|4|5|7|8][0-9]\d{8}$/', $tel)){
            $this->ajaxReturn('err');
            exit;
        }else{
            $sms = $this->shortMessage($tel);
            $this->ajaxReturn($sms);
        }

    }

    public function emailReset()
    {
             $pass =  md5($_POST['u_password']); 
             $username = I('post.u_username');           
             $wait = D('wait');
             $wData = ['w_uid' =>session('home_id'),'w_email' => $username];

           
             if($wait->create($wData)) {
             	$wid = $wait->add();
             	if($wid){
	             $title = '修改密码验证';   
	            // 发送到邮箱的链接采用base64加密
	            $content = '尊敬的用户'.$username.': 感谢您注册终点中文网，您可以通过点击以下链接激活您的账号: <a href="'.U('Home/Reset/activa',['w_id' => base64_encode($wid),'pass'=>base64_encode($pass)], 'html', true).'">'.U('Home/Reset/activa',['w_id' => base64_encode($wid),'pass'=>base64_encode($pass)], 'html', true).'</a>';
	          
	            if($this->sendEmail($username, $title, $content)) {
	                $this->success('邮件发送成功，请前往激活您的账号！');
	            } else {
	                $this->error('修改失败，请稍后再试！');
	       		 }
	        }else{
	        	$this->error('修改失败，请稍后再试！');
	        }
       }else{
       		$this->error('修改失败，请稍后再试！1');
       }
    }

    public function activa($w_id = null,$pass=null)
    {
        if(empty($w_id)) {
            $this->redirect('Home/Index/index');
        }

        $w_id =  base64_decode($w_id);
        $pass =  base64_decode($pass);
        $res = D('wait')->where(['w_id' => $w_id])->find();
        /*var_dump($res);
        die;*/
        if(!$res) {
            $this->error('验证出错，请重新修改！', U('Home/Center/reset'));
        }

        D('user')->where(['u_id'=>$res['w_uid']])->save(['u_password'=>$pass]);
       
       	
        D('wait')->where(['w_id' => $w_id])->delete();

        $this->success('修改成功!', U('Home/Center/index'));


    }
    
  

  
}

