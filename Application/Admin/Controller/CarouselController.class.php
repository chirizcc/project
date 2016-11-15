<?php
namespace Admin\Controller;

use Think\Controller;

class CarouselController extends AdminController
{
	public function index()
	{
		$map = [];
	    $map['t_pid'] = $pid;

		$data = M('play')->where($map)->select();
		$this->assign('type',$data);
		$this->display();
	}

	public function add()
	{
		$this->display();
	}

	public function picupload()
	{
	    $upload = new \Think\Upload();// 实例化上传类
	    $upload->maxSize   =     3145728 ;// 设置附件上传大小
	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
	    $upload->savePath  =     'Carousel'; // 设置附件上传（子）目录
	    // 上传文件 
	    $info   =   $upload->upload();
	    if(!$info) {// 上传错误提示错误信息
       		 $this->ajaxReturn($upload->getError());
	    }else{// 上传成功
	    	//获取上传后的路径和文件名
	        $path = './Uploads/'.$info['file']['savepath'];
        	$name = $info['file']['savename'];
        	$a = ltrim($path.$name,'.');
        	$this->ajaxReturn($a);
	    }
	}

}