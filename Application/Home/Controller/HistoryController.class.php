<?php
namespace Home\Controller;

//浏览历史控制器
class HistoryController extends HomeController
{
	//浏览历史首页
	public function index()
	{	
		//判断是否登录  没有登录阻止进入个人中心
		if(empty(session('home_id'))){
			$this->redirect('Index/index');
		}
		$id = session('home_id');
		$data = M('history')->table('zd_history as h,zd_book as b')->field('b.b_img img,h.h_time time,b.b_name bookname,b.b_author author,b.b_id id')->order(array('h_time'=>'desc'))->where('h.h_bid = b.b_id and h.h_uid ='.$id)->select();
		$this->assign('data',$data);
		$this->display();
	}

	//书籍详情页 点击阅读判断
	public function read()
	{
		if(empty($_GET['b_id'])){
			$this->redirect('Index/index');
		}

		$map = [];
		$map['h_bid'] = $_GET['b_id'];
		$map['h_uid'] = session('home_id');
		$where = [];
		$where['cata_bid'] = $_GET['b_id'];
		
		//判断是否登录，没有登录直接从第一章开始阅读
		if(empty(session('home_id'))){
			//没有登录 查出书本的第一章
			$firstcata = M('catalog')->where($where)->order('cata_order')->select();
			$this->redirect('Read/index',array('cata_id'=>$firstcata[0]['cata_id']));
			exit;
		}
		//判断历史记录中是否有这本书 有的话从历史记录的章节开始读 没有的话从第一章开始读
		$history = M('history')->where($map)->select();
		if(empty($history)){
			//历史记录为空的话 查出书本的第一章
			$firstcata = M('catalog')->where($where)->order('cata_order')->select();
			$this->redirect('Read/index',array('cata_id'=>$firstcata[0]['cata_id']));
		}else{
			$wheres = [];
			$wheres['h_bid'] = $_GET['b_id'];
			$cata = M('history')->where($wheres)->field('h_cata')->select();
			$this->redirect('Read/index',array('cata_id'=>$cata[0]['h_cata']));
		}
	}
}