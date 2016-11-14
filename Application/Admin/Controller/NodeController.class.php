<?php

namespace Admin\Controller;

// 用户权限管理控制器
class NodeController extends AdminController
{
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

    public function delRole($u_id,$r_id)
    {
        if(M('user_role')->where(['ur_uid' => $u_id,'ur_rid' => $r_id])->delete()) {
            $this->success('删除角色成功！');
        } else {
            $this->error('删除角色失败！请稍后再试！');
        }
    }
}