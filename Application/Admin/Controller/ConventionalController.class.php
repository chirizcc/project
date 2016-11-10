<?php 
namespace Admin\Controller;
use Think\Controller;

class ConventionalController extends AdminController
{
	public function index() {
		
		$server=$_SERVER;
		$this->assign('server',$server);

		$this->display('Conventional/index');
	}

}
