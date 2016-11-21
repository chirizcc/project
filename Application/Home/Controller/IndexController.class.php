<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends HomeController
{
    public function index()
    {
		//$this->getPromode();

    	$map = [];
    	$map['p_type'] = 0;
    	$play = M('play')->where($map)->select();
    	$count = M('play')->where($map)->count();// 查询满足要求的总记录数
    	// var_dump($play);exit;
    	$this->assign('play',$play);
    	$this->assign('count',$count);
        $this->assign('newBooks',$this->getNewBooks());
    	$this->assign('proList',$this->getPromode());
        $this->display();
    }

    // 获取推荐书籍
	private function getPromode()
	{
		$pro = M('promode');
		$data = $pro->table('zd_book b,zd_promode p')->where('b.b_id = p.pro_bookid')->field('b.b_id,b_name,b_img,b_author')->order('p.pro_id desc')->limit(6)->select();

        return $data;
	}

    // 获取新上架书籍
	private function getNewBooks()
    {
        $data = M('book')->where(['b_status' => 1])->field('b_id,b_name,b_img')->order('b_id desc')->limit(15)->select();
        return $data;
    }

}