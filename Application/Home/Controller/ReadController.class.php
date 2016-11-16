<?php
namespace Home\Controller;
use Think\Controller;

//阅读页面控制器
class ReadController extends HomeController
{
	public function index()
	{
		$this->display();
	}
}