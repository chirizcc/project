<?php 
	namespace Admin\Controller;
	use Think\Controller;

	class BookController extends AdminController
	{
		public function index() {
			$data = M('book')->order('b_id desc')->select();
			$this->assign('list',$data);
			$this->assign('title','书籍管理');
			$this->assign('bookname','书籍名称');
			$this->assign('img','图片');
			$this->assign('click','访问数量');
			$this->assign('status','状态');
			$this->assign('time','上传时间');
			$this->display();
		}

		public function del()
	    {
	       
	        if (empty($_GET['id'])) {
	            $this->redirect('Admin/Book/index');
	            exit;
	        }
	     
	        $id = I('get.id/d');	       
	        if (M('book')->delete($id) > 0) {
	           $this->success('恭喜您,删除成功!', U('index'));
	        } else {
	           $this->error('删除失败....', U('index'));
	        }
	    }

	    //添加页面
	    public function add()
	    {	
	    
	    	$this->assign('title1','书籍添加');
	    	$this->assign('title2','填写书籍内容');
	    	$data = M('type')->where('t_pid=0')->order('t_id desc')->select();
	        $this->assign('list',$data);
	         	       
	        $this->assign('title','添加用户');
	        $this->display('Book/add');
	        // echo M('type')->getLastSql().'<br>';

	    }
		public function getadd()
		{	
			$b_id = I('get.b_id/d');
			
			$data = M('type')->where('t_pid='.$b_id)->select();
			$json_d = json_encode($data);
			$this->ajaxReturn($json_d);
			// var_dump($data);
			$this->assign('data',$data);

		}

		public function insert() {

	        if (empty($_POST)) {
	            $this->redirect('Admin/Book/add');
	            exit;
	        }
	        // var_dump($_POST);
	     	 M('book')->create();
			
			if (M('book')->add() > 0) {
	           $this->success('恭喜您,添加成功!', U('index'));
	        } else {
	           $this->error('添加失败....');
	        }
		}

		public function edit()
		{
			// 接收id
			$id = I('get.id/d');
			//查找
       		$data = M('book')->find($id);
       		$this->assign('data',$data);      
			$this->assign('title1','书籍修改');
	    	$this->assign('title2','修改书籍内容');
	    	$this->display('Book/edit');
		}


		 public function update()
	    {
	        if (empty($_POST)) {
	            $this->redirect('Admin/User/add');
	            exit;
	        }
	        
	        var_dump($_POST);
	        // M('book')->create();
	        // //执行修改
	        // if (M('book')->save() > 0) {
	        //    $this->success('恭喜您,编辑成功!', U('index'));
	        // } else {
	        //    $this->error('编辑失败....');
	        // }
	    }
		
	}