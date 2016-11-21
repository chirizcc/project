<?php  
	namespace Home\Controller;
	use Think\Controller;

	// 作者编辑
	class AuthoreditController extends JudgeController
	{
		private $size = 6;
		public function index()
		{
			$p = $_GET['p'];
        if ($p == null) {
            $p = 0;
        }

        $map = [];
        if(!empty($search)) {
            $map['b_name'] =  ['like','%'.$search.'%'];
        }

			// 查询作者的小说
        $data = M('book')->where($map)->where('b_uid ='.session('home_id'))->order('b_id desc')->page($p, $this->size)->select();
        $count = M('book')->where($map)->count();// 查询满足要求的总记录数


        $Page = new \Org\Util\MyPage($count, $this->size);

        if(!empty($search)) {
            //分页跳转的时候保证查询条件
            foreach($map as $key=>$val) {
                $Page->parameter[$key] = urlencode($val);
            }
        }

        $show = $Page->show();
        $this->assign('list', $data);
        $this->assign('page', $show);
        $this->display();

		}

		public function add()
		{	
			$data = M('type')->where('t_pid=0')->order('t_id desc')->select();
        	$this->assign('list', $data);
			$this->display();
		}

		// 地址联动
		public function getadd()
		{
			$b_id = I('get.b_id/d');

	        $data = M('type')->where('t_pid=' . $b_id)->select();
	        $json_d = json_encode($data);
	        $this->ajaxReturn($json_d);

	        $this->assign('data', $data);
		}

		// 添加
		 public function insert()
	    {

	        if (empty($_POST)) {
	            $this->redirect('Admin/Book/add');
	            exit;
	        }

	        if(I('post.b_tid') == 0 || I('post.b_tid') == null) {
	            $this->error('请选择分类');
	        }

	        $bookModel = D('book');
	        if($bookModel->create()) {
	            if ($bookModel->add() > 0) {
	                $this->success('恭喜您,添加成功!', U('index'));
	            } else {
	                $this->error('添加失败....');
	            }
	        } else {
	            $this->error($bookModel->getError());
	        }
	    }
	}