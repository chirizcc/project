<?php
namespace Admin\Controller;
use Think\Controller;

class TypeController extends AdminController
{
	public function index($pid = 0){
		$time = date('Y-m-d',time());
		$type = M('type');

		//判断get是否由传参  没有的话显示顶级目录
		$pid = I('get.pid');
		// var_dump($pid);
		if($pid == 0){
			$map = [];
		    $map['t_pid'] = $pid;
            //分页
            $count = $type->where($map)->count();// 查询满足要求的总记录数
            // var_dump($count);exit;
            $Page =  new \Org\Util\MyPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数
            // $Page->setConfig('header','个会员');
            $show = $Page->show();// 分页显示输出

			$data = $type->where($map)->page($_GET['p'],'6')->select();
            $this->assign('page',$show);// 赋值分页输出
			$this->assign('type',$data);
		}else{
			$map = [];
		    $map['t_pid'] = $pid;

			$data = $type->where($map)->select();
			$this->assign('type',$data);
		}


		$this->assign('time',$time);
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
        // $id = $_GET['id'];
        // I() 方法 过滤输入的数据 
        $id = I('get.id/d');
        // echo $id;exit;

        //判断是否有子分类
        $type = M('type');
        $map = [];
		$map['t_pid'] = $id;
		$data = $type->where($map)->select();
		if(!empty($data)){
			/*echo "<script>alert('该类别还有子分类，无法删除');</script>";
			$this->redirect('Type/index', array('tip' => '该类别还有子分类，无法删除'));
			exit;*/
            $this->error('删除失败....', U('index'));
		}

        //执行删除
        if ($type->delete($id) > 0) {
           $this->success('恭喜您,删除成功!', U('index'));
        } else {
           $this->error('删除失败....', U('index'));
        }
    }

    //添加页面
    public function add()
    {
    	$type = M('type');
    	$map = [];
		$map['t_pid'] = 0;
    	$data = $type->where($map)->select();
    	$time = date('Y-m-d',time());
    	$this->assign('time',$time);
    	$this->assign('list',$data);
        $this->display('Type/add');
    }

    //执行添加
    public function insert()
    {
        if (empty($_POST['t_name'])) {
            $this->redirect('Admin/Type/add');
            exit;
        }
        // 拼接$_POST['t_path'];
        if($_POST['t_pid'] == 0){
        	$_POST['t_path'] = $_POST['t_pid'].",";
        }else{
        	$_POST['t_path'] = "0,".$_POST['t_pid'];
        }

        //1.自己手动过滤POST数据
        //2.M('user')->data()  自动生成数据
        //3.推荐!
        M('type')->create();

        //执行添加
        if (M('type')->add() > 0) {
           $this->success('恭喜您,添加成功!', U('index'));
        } else {
           $this->error('添加失败....');
        }
    }

    //编辑页面
    public function edit($id)
    {
    	if (empty($_GET['id'])) {
            $this->redirect('Admin/Type/index');
            exit;
        }

        //接收ID
        $id = I('get.id/d');
        //查找
    	$type = M('type');
        $list = $type->find($id);

        $time = date('Y-m-d',time());
    	$this->assign('time',$time);

        $this->assign('list',$list);
        $this->display('Type/edit');
    }

    //执行修改
    public function update()
    {
        if (empty($_POST)) {
            $this->redirect('Admin/Type/edit');
            exit;
        }
        $type = M('type');

        $type->create();
        //执行修改
        if (M('type')->save() > 0) {
           $this->success('恭喜您,编辑成功!', U('index'));
        } else {
           $this->error('编辑失败....');
        }
    }
}