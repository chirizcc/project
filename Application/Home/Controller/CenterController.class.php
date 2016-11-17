<?php

namespace Home\Controller;

// 个人中心控制器
class CenterController extends JudgeController
{
    public function index()
    {
        $u_id = session('home_id');
        $img = M('detail')->where(['det_uid' => $u_id])->field('det_img')->find();
        $this->assign('img',$img['det_img']);
        $this->display();
    }

    public function show()
    {
        $this->display();
    }

    public function info()
    {
        $u_id = session('home_id');
        $data = M('detail')->where(['det_uid' => $u_id])->find();

        $res = M('user')->field('u_regtime')->find($u_id);
        $data['regTime'] = date('Y-m-d H:i',$res['u_regtime']);

        $this->assign('data',$data);
        $this->display();
    }
    
    public function edit()
    {
        $u_id = session('home_id');
        $data = M('detail')->where(['det_uid' => $u_id])->find();
        $this->assign('data',$data);
        $this->display();
    }
    
    public function update()
    {
        $de = D('detail');
        if($de->create()) {
            if(false === $de->save()) {
                $this->error('编辑失败，请稍后再试!');
            } else {
                $this->success('编辑成功',U('Home/Center/info'));
            }
        } else {
            $this->error($de->getError());
        }
    }
}