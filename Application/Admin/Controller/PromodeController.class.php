<?php
namespace Admin\Controller;

use Think\Controller;

class PromodeController extends AdminController
{	
	public function index()
	{	$p = $_GET['p'];
        if ($p == null) {
            $p = 0;
        }
		$book= D('book');
		$data = $book->table('zd_book b,zd_promode p')->where('b.b_id=p.pro_bookid')->page($p, 6)->select();
		$count =M('promode')->count();
		$Page = new \Org\Util\MyPage($count, 6);
        $show = $Page->show();
        $this->assign('page', $show);
		$this->assign('list',$data);
		$this->display();
	}


	public function pro() 
	{	$p = $_GET['p'];
        if ($p == null) {
            $p = 0;
        }
        $data = M('book')->where('b_status=1')->order('b_id desc')->page($p, 6)->select();
        $count = M('book')->where('b_status=1')->count();
        $Page = new \Org\Util\MyPage($count, 6);
        $show = $Page->show();
        $this->assign('page', $show);
       
        $this->assign('list', $data);
		$this->display();
	}

	public function proadd()
	{	
		if (empty($_GET['id'])) {
            $this->redirect('index');
            exit;
        }
		$id['pro_bookid'] =I('get.id/d') ;

		if(M('promode')->add($id) > 0){
			$this->success('推广成功!', U('index'));
		}else{
			$this->error('推广失败!', U('index'));
		}
	}

	public function delpro()
	{
		if (empty($_GET['id'])) {
            $this->redirect('index');
            exit;
        }
        $id = I('get.id/d');
        if(M('promode')->where('pro_bookid='.$id)->delete() > 0){
			$this->success('取消推广成功!', U('index'));
		}else{
			$this->error('取消推广失败!', U('index'));
		}
	}



}