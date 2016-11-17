<?php 
namespace Admin\Controller;
use Think\Controller;

class ConventionalController extends Controller
{
	public function index() {
		// 获取系统数据
		$server=$_SERVER;
		// 发送系统数据
		$this->assign('server',$server);		
		$this->display('Conventional/index');
	}

}
