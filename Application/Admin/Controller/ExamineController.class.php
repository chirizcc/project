<?php

namespace Admin\Controller;

// 书籍审核控制器
class ExamineController extends AdminController
{
	//书籍审核
	public function index()
	{
		$book = M('book');
		$map = [];
		$map['b_status'] = 0;
		//分页
		$count = $book->where($map)->count();// 查询满足要求的总记录数
		$Page =  new \Org\Util\MyPage($count,3);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$data = $book->order('b_id desc')->where($map)->page($_GET['p'],'3')->select();
		// var_dump($data);exit;
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('data',$data);
		$this->display();
	}

	//书籍审核通过
	public function allow()
	{
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Examine/index');
            exit;
        }
        //接收参数
        $id = I('get.id/d');

        $book = M('book');
        //拼接条件
        $map = [];
        $map['b_id'] = $id;
        //拼接修改字段
        $where = [];
        $where['b_status'] = '1';
        if($book->where($map)->save($where) !== false){
        	$this->success('恭喜您,审核通过成功!', U('index'));
        }else{
        	$this->error('通过审核失败');
        }

	}

	//书籍审核不通过
	public function forbid()
	{
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Examine/index');
            exit;
        }
        //接收参数
        $id = I('get.id/d');	

        $book = M('book');
        //拼接条件
        $map = [];
        $map['b_id'] = $id;
        //拼接修改字段
        $where = [];
        $where['b_status'] = '3';
        if($book->where($map)->save($where) !== false){
        	$this->success('下架书本成功', U('index'));
        }else{
        	$this->error('下架书本失败');
        }	
	}

	//作者审核
	public function author()
	{
		$user = M('user');
		$map = [];
		$map['u_istype'] = 2;
		//分页
		$count = $user->where($map)->count();// 查询满足要求的总记录数
		$Page =  new \Org\Util\MyPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$data = $user->order('u_id desc')->where($map)->page($_GET['p'],'6')->select();
		// var_dump($data);exit;
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('data',$data);
		$this->display();
	}

	//作者审核通过
	public function autallow()
	{
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Examine/author');
            exit;
        }
        //接收参数
        $id = I('get.id/d');

        $user = M('user');
        //拼接条件
        $map = [];
        $map['u_id'] = $id;
        //拼接修改字段
        $where = [];
        $where['u_istype'] = '1';
        if($user->where($map)->save($where) !== false){
        	$this->success('恭喜您,审核通过!', U('Examine/author'));
        }else{
        	$this->error('通过审核失败');
        }

	}

	//作者审核不通过
	public function autforbid()
	{
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Examine/author');
            exit;
        }
        //接收参数
        $id = I('get.id/d');	

        $user = M('user');
        //拼接条件
        $map = [];
        $map['u_id'] = $id;
        //拼接修改字段
        $where = [];
        $where['u_istype'] = '3';
        if($user->where($map)->save($where) !== false){
        	$this->success('拒绝通过成功', U('Examine/author'));
        }else{
        	$this->error('拒绝通过失败，请重新拒绝');
        }	
	}

	//内容审核
	public function content()
	{
		$cat = M('catalog');
		$map = [];
		$map['cata_status'] = '0';
		// $map['c.cata_bid'] = 0;
		//分页
		$count = $cat->where($map)->count();// 查询满足要求的总记录数
		$Page =  new \Org\Util\MyPage($count,5);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$data = $cat->table('zd_catalog as c,zd_book as b')->where('c.cata_bid = b.b_id')->where($map)->field('c.cata_id id,b.b_name bookname,c.cata_name name,b.b_author author')->page($_GET['p'],'5')->select();
		// var_dump($data);exit;
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('data',$data);
		$this->display();
	}

	//内容审核通过
	public function contAllow()
	{
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Examine/content');
            exit;
        }
        //接收参数
        $id = I('get.id/d');

        $catalog = M('catalog');
        //拼接条件
        $map = [];
        $map['cata_id'] = $id;
        //拼接修改字段
        $where = [];
        $where['cata_status'] = '1';
        if($catalog->where($map)->save($where) !== false){
        	$this->success('恭喜您,审核通过!', U('Examine/content'));
        }else{
        	$this->error('通过审核失败');
        }

	}

	//内容审核不通过
	public function contForbid()
	{
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Examine/content');
            exit;
        }
        //接收参数
        $id = I('get.id/d');	

        $catalog = M('catalog');
        //拼接条件
        $map = [];
        $map['cata_id'] = $id;
        //拼接修改字段
        $where = [];
        $where['cata_status'] = '2';
        if($catalog->where($map)->save($where) !== false){
        	$this->success('拒绝通过成功', U('Examine/content'));
        }else{
        	$this->error('拒绝通过失败，请重新拒绝');
        }	
	}

}