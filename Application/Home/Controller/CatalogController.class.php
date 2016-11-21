<?php  
	namespace Home\Controller;
	use Think\Controller;

	class CatalogController extends JudgeController
	{	
		private $size = 5;
		

		public function __construct()
	    {
	        parent::__construct();
	        if(ACTION_NAME != 'index') {
	            if (empty(session('b_id'))) {
	                $this->redirect('Home/Authoredit/index');
	            }
	        }
	    }
		public function index($b_id = null,$search = null)
		{	

			$p = I('get.id/d');
	        if ($p == null) {
	            $p = 0;
	        }
			if(empty($b_id)) {
	            if(empty(session('b_id'))) {
	                $this->redirect('Home/Authoredit/index');
	                die;
	            } else {
	                $b_id = session('b_id');
	            }
	        }

	        // 搜索条件
	        $map = [];
	        if(!empty($search)) {
	            $map['cata_name'] =  ['like','%'.$search.'%'];
	        }

	        $result = M('book')->where(['b_id' => $b_id])->field('b_name')->find();
        	$name = $result['b_name'];

        	session('b_id',$b_id);
        	session('b_name',$name);
        	// 查询数据库
        	$data = M('catalog')->where(['cata_bid' => $b_id])->where($map)->order('cata_order')->page($p, $this->size)->select();
        	// 查询满足要求的总记录数
        	$count = M('catalog')->where(['cata_bid' => $b_id])->where($map)->count();
			 $Page = new \Org\Util\MyPage($count, $this->size);// 实例化分页类 传入总记录数和每页显示的记录数

	        if(!empty($search)) {
	            //分页跳转的时候保证查询条件
	            foreach($map as $key=>$val) {
	                $Page->parameter[$key] = urlencode($val);
	            }
	        }
	    
	        $show = $Page->show();// 分页显示输出
	        $this->assign('page',$show);// 赋值分页输出
	        $this->assign('data',$data);
	        $this->display();
			}

		public function add()
	    {
	        $this->display();
	    }

	    public function insert()
	    {
	        $catalog = M('catalog');
	        $data = I('post.');

	        if (empty(I('post.cata_name'))) {
	            $this->error('章节名不能为空！');
	            die;
	        }

	        $res = $catalog->where(['cata_bid' => session('b_id')])->max('cata_order');
	        $order = $res + 1;
	        $data['cata_order'] = $order;

	        if($catalog->create($data)) {
	            if(($cata_id = $catalog->add()) > 0) {
	                $data = ['con_catid' => $cata_id];
	                $content = D('content');
	                if($content->create($data)) {
	                    if($content->add() > 0) {
	                        $this->success('添加成功！',U('Home/Catalog/index',['b_id' => session('b_id')]));
	                    }
	                } else {
	                    $catalog->delete($cata_id);
	                }
	            }
	        }

	        $this->error('添加失败，请稍后再试！');
	    }

	    public function edit($cata_id = null)
	    {
	        if(empty($cata_id)) {
	            $this->redirect('Home/Authoredit/index');
	            die;
	        }

	        $data = M('catalog')->find($cata_id);
	        $this->assign('data',$data);
	        $this->display();
	    }

	    public function update()
	    {
	        $cata = M('catalog');
	        if($cata->create()) {
	            if($cata->save()) {
	                $this->success('修改成功',U('Home/Catalog/index',['b_id' => session('b_id')]));
	            } else {
	                $this->error('修改失败，请稍后再试！');
	            }
	        } else {
	            $this->error($cata->getError());
	        }
	    }
	}