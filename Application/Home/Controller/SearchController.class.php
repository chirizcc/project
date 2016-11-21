<?php
namespace Home\Controller;

use Think\Controller;

class SearchController extends HomeController
{
	public function index()
	{
		//判断有无参数传递过来 没有当做空字符串处理
		if(empty($_POST['val'])){
			$this->ajaxReturn('a');
			exit;
		}

		//获取字符串内容
		$str = $_POST['val'];
		//$str = trim($str);//去空格

		//输入空格不显示
		if($str == ''){
			$this->ajaxReturn('a');
			exit;
		}

		//进行搜索
		$map = [];
		$map['b_name'] = ['like','%'.$str.'%'];

		$book = M('book');
		$data = $book->where($map)->limit(5)->select();
		if(empty($data)){
			$this->ajaxReturn('a');
		}else{
			$this->ajaxReturn($data);
		}
	}

	public function search()
	{
		if(empty($_POST['search'])){
			$this->redirect('Index/index');
			exit;
		}
		$search = $_POST['search'];
		var_dump($search);exit;
	}

}