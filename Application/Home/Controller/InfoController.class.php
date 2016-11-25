<?php

namespace Home\Controller;

// 消息控制器
class InfoController extends JudgeController
{
	public function index()
	{
		$info = M('info');

		$map = [];
		// 未读信息
		$map['i_status'] = '0';
		$map['i_uid'] = session('home_id');

        $count = $info->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Org\Util\MyPage($count, 3);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出

		$data = $info->where($map)->page($_GET['p'], '3')->select();

		$maps = [];
		// 已读信息
		$maps['i_status'] = '1';
		$maps['i_uid'] = session('home_id');
		$counts = $info->where($maps)->count();// 查询满足要求的总记录数
        $Pages = new \Org\Util\Pages($counts, 4);// 实例化分页类 传入总记录数和每页显示的记录数
        $shows = $Pages->show();// 分页显示输出

		$list = $info->where($maps)->page($_GET['pg'], '4')->select();

		$this->assign('page', $show);// 未读消息赋值分页输出
		$this->assign('pages', $shows);// 已读消息赋值分页输出
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->display();
	}


	//标记为已读
	public function readed()
	{	
		//判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Home/Index/index');
            exit;
        }

		$id = I('get.id/d');

		$info = M('info');

		$map = [];
		$map['i_id'] = $id;

		$where = [];
		$where['i_status'] = '1';

		//修改
		if($info->where($map)->save($where) !== false){
			 	$this->success('标记成功!', U('index'));
			}else{
				$this->error('失败，请重试');
			}
	}

}