<?php

namespace Admin\Controller;

// 评论管理控制器
class CommentController extends AdminController
{

    //首页控制器
	public function index($search = null,$pid = null,$bkid = null)
	{
		$com = M('comment');
		//判断搜索条件
		$map = [];
        if(!empty($search)){
            $map['u.u_username'] =  ['like','%'.$search.'%'];
        }		
        //判断类别
        $pidmap = [];
        if(!empty($pid)){
            $pidmap['b.b_tid'] =  $pid;
        }
        //判断类别
        $bkidmap = [];
        if(!empty($bkid)){
            $bkidmap['b.b_id'] =  $bkid;
        }

		$count = $com->table('zd_user as u,zd_comment as c,zd_book as b')->where('c.com_bid = b.b_id and c.com_uid = u.u_id')->where($map)->where($pidmap)->where($bkidmap)->count();
		$Page =  new \Org\Util\MyPage($count,5);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出

		$data = $com->table('zd_user as u,zd_comment as c,zd_book as b')->where('c.com_bid = b.b_id and c.com_uid = u.u_id')->where($map)->where($pidmap)->where($bkidmap)->field('c.com_id id,c.com_time time,c.com_content content,b.b_name bookname,u.u_username username')->page($_GET['p'],'5')->select();

        if(!empty($search)) {
            //分页跳转的时候保证查询条件
            foreach($map as $key=>$val) {
                $Page->parameter[$key] = urlencode($val);
            }
        } 

		//查出第一级分类的数据 用于地址联动
		$map = [];
		$map['t_pid'] = 0;
		$ftype = M('type')->field('t_name,t_id')->where($map)->select();
		$this->assign('ftype',$ftype);

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

        $map = [];
        $map['com_id'] = $id;
        $data = $com->where($map)->find();
        $message = "您的评论:".$data['com_content']."被管理员删除，请遵守网络公约，不要发表不适当的言论！";
        $info = [];
        $info['i_uid'] = $data['com_uid'];
        $info['i_info'] = $message;
        $info['i_time'] = time();
        //将消息发给作者
        if(M('info')->add($info)){
            if ($com->delete($id) > 0) {
                $this->success('恭喜您,删除成功!', U('index'));
            }else{
                $this->error('删除失败....');
            }
        }else{
            $this->error('删除成功，但是信息发送失败。。。');
        }                     
	}

	//查出第二级分类的数据
	public function selectf()
	{
		if (empty($_POST)) {
            $this->redirect('Admin/Carousel/add');
            exit;
        }

        $p_id = I('post.val');

        $map = [];
        $map['t_pid'] = $p_id; 

        $stype = M('type')->where($map)->select();
        $this->ajaxReturn($stype);


	}

	//查出第二级分类的数据
	public function selects()
	{
		if (empty($_POST)) {
            $this->redirect('Admin/Carousel/add');
            exit;
        }

        $p_id = I('post.val');

        $map = [];
        $map['b_tid'] = $p_id; 

        $ttype = M('book')->where($map)->select();
        $this->ajaxReturn($ttype);
	}

}