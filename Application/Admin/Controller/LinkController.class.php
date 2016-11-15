<?php

namespace Admin\Controller;

// 友情链接控制器
class LinkController extends AdminController
{
    public function index()
    {
        $link = D('link');
        $data = $link->order('l_id desc')->select();
        $this->assign('data',$data);
        $this->display();
    }

    public function add()
    {
        $this->display();
    }

    public function insert()
    {
        $link = D('link');
        if($link->create()) {
            if($link->add()) {
                $this->success('添加成功',U('Admin/Link/index'));
            } else {
                $this->error('添加失败 ！请稍后再试！');
            }
        } else {
            $this->error($link->getError());
        }
    }

    public function changeState()
    {
        $link = D('link');
        
        $data = [
            'status' => 1,
            'msg' => '',
        ];
        
        if($link->create()) {
            if($link->save()) {
                $data['status'] = 0;
                $data['msg'] = '修改状态成功';
            } else {
                $data['msg'] = '修改状态失败 ！请稍后再试！';
            }
        } else {
            $data['msg'] = $link->getError();
        }
        
        $this->ajaxReturn($data);
    }

    public function edit($l_id = null)
    {
        if(empty($l_id)) {
            $this->redirect('Admin/Link/index');
        }

        $data = D('link')->find($l_id);
        
        $this->assign('data',$data);
        $this->display();
    }

    public function update()
    {
        $link = D('link');
        $data = I('post.');
        if(!isset($data['state'])) {
            $data['state'] = 1;
        }
        if($link->create()) {
            if($link->save()) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败 ！请稍后再试！');
            }
        } else {
            $this->error($link->getError());
        }
    }
}