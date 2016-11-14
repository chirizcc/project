<?php

namespace Admin\Controller;

// 用户权限管理控制器
class NodeController extends AdminController
{
    private $size = 8;

    public function index()
    {
        $data = M('node')->page($_GET['p'],$this->size)->select();
        $count = M('node')->count();
        $Page = new \Org\Util\MyPage($count,$this->size);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('data',$data);
        $this->display();
    }
    
    // 添加权限表单
    public function add()
    {
        $this->display();
    }
    
    public function insert()
    {
        if(empty(trim(I('post.n_name')))) {
            $this->error('权限名不能为空！');
        }

        if(empty(trim(I('post.n_controller')))) {
            $this->error('权限控制器不能为空！');
        }

        if(empty(trim(I('post.n_action')))) {
            $this->error('权限操作不能为空！');
        }

        $data['n_name'] = trim(I('post.n_name'));
        // 转换为首字母大写
        $data['n_controller'] = ucfirst(strtolower(trim(I('post.n_controller'))));
        // 转换为小写
        $data['n_action'] = strtolower(trim(I('post.n_action')));

        if(M('node')->where(['n_controller' => I('post.n_controller'),'n_action' => I('post.n_action')])->find()) {
            $this->error('该权限已存在，请不要重复添加！');
        }

        if(!M('node')->create($data)){
            $this->error('添加失败！请稍后再试！');
        }

        if(!M('node')->add()) {
            $this->error('添加失败！请稍后再试！');
        }

        $this->success('添加成功！',U('Admin/Node/index'));
    }

    // 用户角色
    public function role($u_id)
    {
        $user = M('user')->field('u_id,u_username')->find($u_id);
        $roleList = M('role')->select();
        $data = M('role')->table('zd_role r,zd_user_role ur')->where('r.r_id =ur.ur_rid and ur.ur_uid = '.$u_id)->field('r.r_id,r.r_name')->select();

        $this->assign('data',$data);
        $this->assign('roleList',$roleList);
        $this->assign('user',$user);
        $this->display();
    }
    
    // 给用户添加角色
    public function addRole()
    {
        $data['ur_uid'] = I('post.ur_uid');
        $data['ur_rid'] = I('post.ur_rid');

        if(M('user_role')->where($data)->find()) {
            $this->error('该用户已有该角色，请勿重复添加！');
        }

        M('user_role')->create();

        if(M('user_role')->add()) {
            $this->success('添加角色成功！');
        } else {
            $this->error('添加角色失败！请稍后再试！');
        }
    }

    // 删除用户的角色
    public function delRole($u_id,$r_id)
    {
        if(M('user_role')->where(['ur_uid' => $u_id,'ur_rid' => $r_id])->delete()) {
            $this->success('删除角色成功！');
        } else {
            $this->error('删除角色失败！请稍后再试！');
        }
    }
}