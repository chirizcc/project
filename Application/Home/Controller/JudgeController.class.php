<?php
/**
 * 前台需要判断登录状态的控制器的基类
 * author 张德昌
*/
namespace Home\Controller;

class JudgeController extends HomeController
{
    /**
     * 构造方法 判断用户是否已经登录
    */
    public function __construct()
    {
        parent::__construct();
        if(empty(session('home_name'))){
            $this->error('请先登录，再进行该操作',U('Home/Login/index'));
        }
    }
}