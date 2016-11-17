<?php

namespace Admin\Controller;

use Think\Controller;

// 后台基类控制器
class AdminController extends Controller
{
    public function __construct(){
        parent::__construct();
		if(empty(session('name'))){
            $this->redirect('Login/index');
    	}

        // admin用户不受权限制限
        if(session('name') != 'admin') {
            $nodeList = session('nodeList');
            // 判断权限
            if (!isset($nodeList[CONTROLLER_NAME]) && CONTROLLER_NAME != 'Index') {
                $this->error('你没有权限进行该操作',U('Admin/Conventional/index'));
            } else {
                $state = 0;
                foreach ($nodeList[CONTROLLER_NAME] as $value) {
                    if($value == ACTION_NAME){
                        $state = 1;
                        break;
                    }
                }
                if($state == 0) {
                    $this->error('你没有权限进行该操作',U('Admin/Conventional/index'));
                }
            }
        }
	}
	
    public function _empty()
    {
        header('HTTP/1.0 404 not fount');
        $this->display('Public/notFount');
    }
}