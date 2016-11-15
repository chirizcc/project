<?php

namespace Home\Controller;

// 个人中心控制器
class CenterController extends JudgeController
{
    public function index()
    {
        $n_id = session('home_id');
        $img = M('detail')->where(['det_uid' => $n_id])->field('det_img')->find();
        $this->assign('img',$img['det_img']);
        $this->display();
    }

    public function show()
    {
        $this->display();
    }
}