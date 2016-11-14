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

        /*echo CONTROLLER_NAME;
        echo ACTION_NAME;*/

        if(session('name') != 'admin') {
            $nodeList = session('nodeList');
            /*echo CONTROLLER_NAME;
            dump($nodeList[CONTROLLER_NAME]);
            die;*/
            if (!isset($nodeList[CONTROLLER_NAME]) && CONTROLLER_NAME != 'Index') {
                $this->error('你没有权限进行该操作');
            } else {
                $state = 0;
                foreach ($nodeList[CONTROLLER_NAME] as $value) {
                    if($value == ACTION_NAME){
                        $state = 1;
                        break;
                    }
                }
                if($state == 0) {
                    $this->error('你没有权限进行该操作');
                }
            }
        }



//        dump(session('nodeList'));die;
	}
	
    public function _empty()
    {
        header('HTTP/1.0 404 not fount');
        $this->display('Public/notFount');
    }
}