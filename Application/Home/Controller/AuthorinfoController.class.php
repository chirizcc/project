<?php 
	namespace Home\Controller;
	use Think\Controller;

	class AuthorinfoController extends Controller
	{
		public function index($u_id = null)
		{
			if(empty($u_id)){
				$this->redirect('Home/Index/index');
			}
			$user = M('user');
			$map = [];
			$map['u_id'] = $u_id;
			$count = M('book')->where(array('b_uid'=>$u_id,'b_status'=>'1'))->count();
			// var_dump($count);exit();
			$data = $user->table('zd_user as u,zd_detail as d')->where($map)->where('u.u_id = d.det_uid')->field('d.det_name name,d.det_sex sex,d.det_introduce introduce,d.det_img img,u.u_regtime time')->find();
			$list = M('book')->where(array('b_uid'=>$u_id,'b_status'=>'1'))->order('b_id')->select();
			// var_dump($list);exit;
			$this->assign('data',$data);
			$this->assign('count',$count);
			$this->assign('list',$list);
			$this->display();
		}
		
	}
