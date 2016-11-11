<?php
namespace Home\Controller;
use Think\Controller;
class RegController extends HomeController
{
	public function index(){
		if(!empty(session('name')) ){
			$this->redirect('Index/index');
			exit;
		}
		$this->display();
	}
}