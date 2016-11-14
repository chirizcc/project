<?php 
namespace Admin\Controller;
use Think\Controller;

class ConventionalController extends Controller
{
	public function index() {
		$server=$_SERVER;
		$this->assign('server',$server);

		$this->display('Conventional/index');
	}

}
