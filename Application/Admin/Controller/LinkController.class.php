<?php

namespace Admin\Controller;

// 友情链接控制器
class LinkController extends AdminController
{
    private $size = 6;

    // 获取友情链接列表
    public function index()
    {
        $link = D('link');
        $data = $link->order('l_id desc')->page($_GET['p'], $this->size)->select();
        $count = $link->count();
        // 分页类
        $Page = new \Org\Util\MyPage($count, $this->size);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('data',$data);
        $this->display();
    }

    // 添加链接表单
    public function add()
    {
        $this->display();
    }

    // 添加链接存入数据库
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

    // 改变链接状态
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

    // 编辑链接表单
    public function edit($l_id = null)
    {
        if(empty($l_id)) {
            $this->redirect('Admin/Link/index');
        }

        $data = D('link')->find($l_id);
        
        $this->assign('data',$data);
        $this->display();
    }


    // 更新链接信息
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

    // 删除链接
    public function del($l_id = null)
    {
        if(empty($l_id)) {
            $this->ajaxReturn(false);
        }

        if(false === D('link')->delete($l_id)) {
            $this->ajaxReturn(false);
        } else {
            $this->ajaxReturn(true);
        }
    }
}