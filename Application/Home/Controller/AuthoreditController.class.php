<?php  
	namespace Home\Controller;
	use Think\Controller;

	// 作者编辑
	class AuthoreditController extends JudgeController
	{
		private $size = 6;
		public function index()
		{
			$p = I('get.p/d');
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

		// 书籍类别联动
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
	            $this->redirect('Home/Authoredit/add');
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

	     public function edit()
	    {
	        $id = I('get.id/d');
	        if (empty($id)) {
	            $this->redirect('Home/Authoredit/index');
	            die;
	        }

	        $type = M('type')->where('t_pid=0')->order('t_id desc')->select();
	        $this->assign('list', $type);
	        // 接收id

	        //查找
	        $data = M('book')->find($id);
	        $this->assign('data', $data);
	        // 子分类
	        $type2 = M('type')->where('t_id=' . $data['b_tid'])->select();
	        $this->assign('type2', $type2);
	        // 顶级分类
	        $type3 = M('type')->where('t_id=' . $type2[0]['t_pid'])->select();
	        $this->assign('type3', $type3);
	        $this->display('Authoredit/edit');
	    }
	    public function delImg($img = null)
    {
        if (empty($img)) {
            return;
        }

        $path = './Uploads/' . $img;
        unlink($path);
    }


	    public function update()
	    {
	        if (empty($_POST)) {
	            $this->redirect('Admin/User/add');
	            exit;
	        }

	        if (I('post.b_tid') == 0 || I('post.b_tid') == null) {
	            $this->error('请选择分类');
	        }

	        $bookModel = D('book');

	        // 获取原来的图片
	        $oldImg = $bookModel->where(['b_id' => I('post.b_id')])->field('b_img')->find();
	        $oldImg = $oldImg['b_img'];

	        if ($bookModel->create()) {
	            //执行修改
	            if ($bookModel->save() > 0) {
	                // 如果有更改图片则删除旧图片
	                if (I('post.b_img') != $oldImg) {
	                    $this->delImg($oldImg);
	                }
	                $this->success('恭喜您,编辑成功!', U('index'));
	            } else {
	                if (I('post.b_img') != $oldImg) {
	                    $this->delImg(I('post.b_img'));
	                }
	                $this->error('编辑失败....');
	            }
	        } else {
	            if (I('post.b_img') != $oldImg) {
	                $this->delImg(I('post.b_img'));
	            }
	            $this->error($bookModel->getError());
	        }

	    }
	}