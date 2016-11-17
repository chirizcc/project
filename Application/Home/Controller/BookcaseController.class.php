<?php  
	namespace Home\Controller;
	use Think\Controller;

	// 收藏书架
	class BookcaseController extends AdminController
	{
		public function index() 
		{	//判断传过来书籍id
			if (empty($_GET['book'])) {
				$this->redirect('Home');
            	exit;
			}

			//获取用户id
			$col['col_uid'] = session('id');
			
			// 获取页面信息
			$this->display();	

		}
	}