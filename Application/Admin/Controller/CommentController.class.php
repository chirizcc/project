<?php

namespace Admin\Controller;

// 评论管理控制器
class CommentController extends AdminController
{
	public function index($search = null)
	{
		$com = M('comment');
		$map = [];
        if(!empty($search)){
            $map['u.u_username'] =  ['like','%'.$search.'%'];
        }		

		$count = $com->table('zd_user as u,zd_comment as c,zd_book as b')->where('c.com_bid = b.b_id and c.com_uid = u.u_id')->where($map)->count();
		// var_dump($count);exit;
		$Page =  new \Org\Util\MyPage($count,5);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出

		$data = $com->table('zd_user as u,zd_comment as c,zd_book as b')->where('c.com_bid = b.b_id and c.com_uid = u.u_id')->where($map)->field('c.com_id id,c.com_time time,c.com_content content,b.b_name bookname,u.u_username username')->page($_GET['p'],'5')->select();

        if(!empty($search)) {
            //分页跳转的时候保证查询条件
            foreach($map as $key=>$val) {
                $Page->parameter[$key] = urlencode($val);
            }
        }  		
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('data',$data);
		$this->display();
	}

	public function del()
	{
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Type/index');
            exit;
        }
        //接收参数
        $id = I('get.id/d');

        $com = M('comment');
		//执行删除
        if ($com->delete($id) > 0) {
           $this->success('恭喜您,删除成功!', U('index'));
        } else {
           $this->error('删除失败....');
        }
	}
}