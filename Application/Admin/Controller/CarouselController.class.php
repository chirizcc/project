<?php
namespace Admin\Controller;

use Think\Controller;

class CarouselController extends AdminController
{
	public function index()
	{
		 $data = M('play')->table('zd_play as p,zd_book as b')->where($map)->where('p.p_link = b.b_id')->field('p.p_id id,b.b_name name,p.p_type type,p.p_link link')->order('p.p_id desc')->select();
		$this->assign('type',$data);

		$this->display();
	}

	public function add()
	{
		//查出第一级分类的数据
		$map = [];
		$map['t_pid'] = 0;
		$ftype = M('type')->field('t_name,t_id')->where($map)->select();
		$this->assign('ftype',$ftype);
	
		$this->display();
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
        // var_dump($p_id);exit;
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
        // var_dump($p_id);exit;
	}

	//图片上传
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

        	$big = ltrim($path.$name,'.');

	    	$image = new \Think\Image();
            $image->open($path.$name);
            // 按照原图的比例生成一个最大为400*400的缩略图并保存为thumb.jpg
            $image->thumb(200, 200)->save($path.'s_'.$name);

        	$small = ltrim($path.'s_'.$name,'.');

        	$a = array('small' => $small,'big' => $big);

        	$this->ajaxReturn($a);
	    }
	}

	public function insert()
	{
		if (empty($_POST)) {
            $this->redirect('Admin/Type/add');
            exit;
        }
        $a = M('play')->create();
        // var_dump(M('play')->create());exit;

        //执行添加
        if (M('play')->add() > 0) {
           $this->success('恭喜您,添加成功!', U('index'));
        } else {
           $this->error('添加失败....');
        }
	}

	//启用和禁用
	public function del()
	{
		//判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Carousel/index');
            exit;
        }

        //接收参数
        $id = I('get.id/d');

        //判断状态
        $map = [];
        $map['p_id'] = $id;
        $data = M('play')->where($map)->find();
        if($data['p_type'] == 0){
        	$where = [];
        	$where['p_type'] = 1;
        	if((M('play')->where($map)->save($where)) == false){
        		$this->error('修改失败');
        	}else{
				$this->success('修改成功',U('index'));
        	}
        }else{
        	$where = [];
        	$where['p_type'] = 0;
        	if((M('play')->where($map)->save($where)) == false){
        		$this->error('修改失败');
        	}else{
				$this->success('修改成功',U('index'));
        	}
        }
	}

	//编辑
	public function edit()
	{
		
	}

}