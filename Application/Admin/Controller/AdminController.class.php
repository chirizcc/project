<?php
/**
 * 后台控制器基类
 * author 张德昌
 */
namespace Admin\Controller;

use Think\Controller;

class AdminController extends Controller
{
    /**
     * 构造函数 判断登录状态 判断RBAC权限
     */
    public function __construct()
    {
        parent::__construct();
        if (empty(session('name'))) {
            $this->redirect('Login/index');
        }

        // admin用户不受权限制限
        if (session('name') != 'admin') {
            $nodeList = session('nodeList');
            // 判断权限
            if (!isset($nodeList[CONTROLLER_NAME]) && CONTROLLER_NAME != 'Index') {
                $this->error('你没有权限进行该操作', U('Admin/Conventional/index'));
            } else {
                $action = ACTION_NAME;
                if ($action == 'update') {
                    $action = 'edit';
                }

                if ($action == 'insert') {
                    $action = 'add';
                }
                $state = 0;
                foreach ($nodeList[CONTROLLER_NAME] as $value) {
                    if ($value == $action) {
                        $state = 1;
                        break;
                    }
                }
                if ($state == 0) {
                    $this->error('你没有权限进行该操作', U('Admin/Conventional/index'));
                }
            }
        }
    }

    /**
     * 空操作重定向
     */
    public function _empty()
    {
        header('HTTP/1.0 404 not fount');
        $this->display('Public/notFount');
    }
}