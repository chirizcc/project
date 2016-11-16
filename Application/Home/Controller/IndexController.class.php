<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends HomeController
{
    public function index()
    {
    	$map = [];
    	$map['p_type'] = 0;
    	$play = M('play')->where($map)->select();
    	$count = M('play')->where($map)->count();// 查询满足要求的总记录数
    	// var_dump($play);exit;
    	$this->assign('play',$play);
    	$this->assign('count',$count);
        $this->display();
    }

}