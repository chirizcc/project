<?php

namespace Admin\Controller;

// 角色管理控制器
class RoleController extends AdminController
{
    public function index()
    {
        $data = M('role')->select();
        $this->assign('data',$data);
        $this->display();
    }

    public function add()
    {
        $this->display();
    }

    public function insert()
    {
        if(empty(trim(I('post.r_name')))) {
            $this->error('角色名不能为空!',U('Admin/Role/add'));
        }

        M('role')->create();
        if(M('role')->add()) {
            $this->success('添加角色成功！',U('Admin/Role/index'));
        } else {
            $this->error('添加角色失败！请稍后再试！',U('Admin/Role/add'));
        }
    }

    // 分配权限表单
    public function allot($r_id)
    {
        $list = M('node')->select();

        // 重组权限节点数组
        $nodes = [];
        foreach ($list as $v) {
            $nodes[$v['n_controller']][] = $v;
        }
        // 查找出这个角色的现有权限
        $nodeList = M('role_node')->where(array('rn_rid' => $r_id))->field('rn_nid')->select();
        /*foreach($list as $v) {
            // 给已有的权限一个标记用于显示
            foreach ($nodeList as $item) {
                if($v['n_id'] == $item['rn_nid']){
                    $v['checked'] = 'true';
                }
            }
            if($v['n_controller'] == 'User') {
                $userList[] = $v;
            } elseif ($v['n_controller'] == 'Type') {
                $typeList[] = $v;
            } elseif ($v['n_controller'] == 'Book') {
                $bookList[] = $v;
            }
        }*/

        foreach ($nodes as $k => $v) {
            foreach ($v as $key => $value) {
                foreach ($nodeList as $item) {
                    if($value['n_id'] == $item['rn_nid']){
                        $nodes[$k][$key]['checked'] = 'true';
                    }
                }
            }
        }
        /*dump($nodes);

        die;*/
        $data = M('role')->find($r_id);
        /*$this->assign('userList',$userList);
        $this->assign('typeList',$typeList);
        $this->assign('bookList',$bookList);*/
        $this->assign('nodes',$nodes);
        $this->assign('data',$data);
        $this->display();
    }

    // 分配权限
    public function changeNode()
    {
        $r_id = I('post.r_id');
        $node = I('post.node');

        // 先删除这个角色的所有权限 防止重复添加
        M('role_node')->where('rn_rid = '.$r_id)->delete();

        foreach ($node as $value) {
            $dataList[] = array('rn_rid' => $r_id,'rn_nid' => $value);
        }
        M('role_node')->addAll($dataList);
        $this->success('权限分配成功！');
    }
}