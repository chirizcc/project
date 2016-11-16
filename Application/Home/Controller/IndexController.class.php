<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends HomeController
{
    public function index(){
    	$play = M('play')->select();
    	$this->assign('play',$play);
        $this->display();
    }
}