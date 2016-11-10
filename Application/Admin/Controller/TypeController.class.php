<?php
namespace Admin\Controller;
use Think\Controller;

class TypeController extends AdminController
{
	public function index($pid = 0){
		$time = date('Y-m-d',time());
		$type = M('type');

		//判断get是否由传参  没有的话显示顶级目录
		$pid = I('get.pid');
		// var_dump($pid);
		if($pid == 0){
			$map = [];
		    $map['t_pid'] = $pid;

			$data = $type->where($map)->select();
			if(empty($data)){
				
			}
			$this->assign('type',$data);
		}else{
			$map = [];
		    $map['t_pid'] = $pid;

			$data = $type->where($map)->select();
			if(empty($data)){
				
			}
			$this->assign('type',$data);
		}


		$this->assign('time',$time);
		$this->display();
	}
}