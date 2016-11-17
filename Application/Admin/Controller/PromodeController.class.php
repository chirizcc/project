<?php
namespace Admin\Controller;

use Think\Controller;

class PromodeController extends AdminController
{	
	public function index()
	{	//如果 分页传过来参数为空，默认第一页
		$p = $_GET['p'];
        if ($p == null) {
            $p = 0;
        }
        // 
		$book = D('book');
		// 多表联查 查询推广的书籍
		$data = $book->table('zd_book b,zd_promode p')->where('b.b_id=p.pro_bookid')->page($p, 6)->select();
		// 获取总条数
		$count = M('promode')->count();
		$Page = new \Org\Util\MyPage($count, 6);
        $show = $Page->show();
        // 发送分页数据
        $this->assign('page', $show);
        // 发送数据
		$this->assign('list',$data);
		$this->display();
	}


	public function pro() 
	{	//如果 分页传过来参数为空，默认第一页
		$p = $_GET['p'];
        if ($p == null) {
            $p = 0;
        }
        // 获取pro_bookid数据
        $pro = M('promode')->field('pro_bookid')->select();
        // 遍历promode表的pro_bookid
        foreach ($pro as $k => $v) {
        	$row[$k] = $v['pro_bookid'];
        }
      	$bid['b_id'] = array('not in',$row);
      	// 获取已上架又没有被推广的书籍
        $data = M('book')->where('b_status=1')->where($bid)->page($p, 6)->select();
   		// 获取总条数
        $count = M('book')->where('b_status=1') ->count()-M('promode')->count();	      
        $Page = new \Org\Util\MyPage($count, 6);
        $show = $Page->show();
        // 发送分页数据
        $this->assign('page', $show);
        // 发送数据
        $this->assign('list', $data);
		$this->display();
	}

	public function proadd()
	{	//判断
		if (empty($_GET['id'])) {
            $this->redirect('index');
            exit;
        }
		$id['pro_bookid'] = I('get.id/d') ;
		// 判断是否已经推广
		if (M('promode')->where('pro_bookid = '.$_GET['id'])->select()) {
			$this->error('勿重复推广!', U('pro'));
			exit;
		}
		// 判断是否插入数据成功
		if(M('promode')->add($id) > 0){
			$this->success('推广成功!', U('index'));
		}else{
			$this->error('推广失败!', U('index'));
		}
	}

	public function delpro()
	{	//判断参数是否为空
		if (empty($_GET['id'])) {
            $this->redirect('index');
            exit;
        }
        //接收参数
        // $id = $_GET['id'];
        // I() 方法 过滤输入的数据 
        $id = I('get.id/d');
        // 判断取消推广是否成功
        if (M('promode')->where('pro_bookid = '.$id)->delete() > 0){
			$this->success('取消推广成功!', U('index'));
		}else{
			$this->error('取消推广失败!', U('index'));
		}
	}



}