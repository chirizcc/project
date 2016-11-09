<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/7
 * Time: 21:37
 */

namespace Admin\Controller;

use Think\Controller;

class AdminController extends Controller
{
	public function __construct(){
        parent::__construct();
		if(empty(session('name'))){
    		// R('Login/index');
            $this->redirect('Login/index');
    	}
	}
	
    public function _empty()
    {
        header('HTTP/1.0 404 not fount');
        $this->display('Public/notFount');
    }
}