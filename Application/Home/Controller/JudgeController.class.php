<?php

namespace Home\Controller;

class JudgeController extends HomeController
{
    public function __construct()
    {
        parent::__construct();
        if(empty(session('home_name'))){
            $this->error('请先登录，再进行该操作',U('Home/Login/index'));
        }
    }
}