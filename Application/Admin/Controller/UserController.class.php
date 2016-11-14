<?php

namespace Admin\Controller;

class UserController extends AdminController
{
    // 一页显示多少个
    private $size = 8;

    public function index($search = null)
    {
        $map = [];
        if(!empty($search)) {
            $map['u.u_username'] =  ['like','%'.$search.'%'];
        }

        // 获取所有用户信息
        $data = M('user')->table('zd_user as u,zd_detail as d')->where($map)->where('u.u_id = d.det_uid')->field('u.u_id id,u.u_username username,u.u_regtime time,u.u_istype type,d.det_name name,d.det_sex sex')->order('u.u_id desc')->page($_GET['p'], $this->size)->select();

        // 获取所有用户角色信息
        $roles = M('user_role')->table('zd_user_role as ur,zd_role as r')->where('ur.ur_rid = r.r_id')->field('ur.ur_uid as u_id,r.r_name as r_name')->select();

        // 将用户角色信息加入到用户信息中，用于页面显示
        foreach ($roles as $v) {
            foreach ($data as $key => $value) {
                if($v['u_id'] == $value['id']) {
                    $data[$key]['role'][] = $v['r_name'];
                }
            }
        }

        $this->assign('data',$data);

        $count = M('user')->table('zd_user as u,zd_detail as d')->where($map)->where('u.u_id = d.det_uid')->count();// 查询满足要求的总记录数
        // 使用自定义分页类
        //$Page = new \Think\Page($count,1);
        $Page = new \Org\Util\MyPage($count, $this->size);// 实例化分页类 传入总记录数和每页显示的记录数

        if(!empty($search)) {
            //分页跳转的时候保证查询条件
            foreach($map as $key=>$val) {
                $Page->parameter[$key] = urlencode($val);
            }
        }

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        
        $this->display();
    }
    
    public function myInfo()
    {
        $u_id = session('id');
        $model = M('user');
        $data = $model->table('zd_user as u,zd_detail as d')->where('u.u_id = d.det_uid	and u.u_id = %d',$u_id)->field('d.det_id as id,u_username as username,u_regtime as regtime,det_name as name,det_sex as sex,det_tel as tel,det_email as email,det_introduce as introduce,det_img as img')->find();
        $this->assign('data',$data);
        $this->display();
    }

    public function update()
    {
        $user = M('detail');
        $data = I('post.');
        if(false === $user->save($data)) {
            $this->ajaxReturn(false);
        } else {
            $this->ajaxReturn(true);
        }
    }

    public function updatePortrait($det_id)
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728 ;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'Portrait'; // 设置附件上传（子）目录
// 上传文件
        $info = $upload->upload();

        if(!$info) {// 上传错误提示错误信息
            $data = [
                'code' => 1,
                'msg' => $upload->getError(),
            ];
        }else{
            $path = './Uploads/'.$info['file']['savepath'];
            $name = $info['file']['savename'];

            $image = new \Think\Image();
            $image->open($path.$name);
            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
            $image->thumb(200, 200)->save($path.'s_'.$name);

            //删除原图片
            unlink($path.$name);

            $det = M('detail');

            // 获取原来的头像
            $old = M('detail')->where('det_id = '.$det_id)->field('det_img')->find();
            $oldImg = './Uploads/'.$old['det_img'];

            $det->det_img = $info['file']['savepath'] . 's_' . $name;
            $re = M('detail')->where('det_id = '.$det_id)->save();
            if($re) {
                unlink($oldImg);
                // 上传成功
                $data = [
                    'code' => 0,
                    'msg' => '上传成功',
                    'url' => $info['file']['savepath'] . 's_' . $name,
                ];
            } else {
                $data = [
                    'code' => 1,
                    'msg' => '上传失败',
                ];
            }
        }

        $this->ajaxReturn($data);
    }

    public function info($u_id)
    {

        if($u_id == session('id')) {
            $this->redirect('Admin/User/myInfo');
            die;
        }

        $model = M('user');
        $data = $model->table('zd_user as u,zd_detail as d')->where('u.u_id = d.det_uid	and u.u_id = %d',$u_id)->field('u.u_id as u_id,d.det_id as id,u_username as username,u_istype as istype,det_name as name,det_sex as sex,det_tel as tel,det_email as email,det_introduce as introduce,det_img as img')->find();
        // 判断是否有查询到数据
        if($data === null){
            $this->redirect('Admin/User/index');
            die;
        }

        $this->assign('data',$data);
        $this->display();
    }
    
    public function changeType()
    {
        $user = M('user');
        $data = I('post.');

        if(false === $user->save($data)) {
            $this->ajaxReturn(false);
        } else {
            $this->ajaxReturn(true);
        }
    }

    public function del($u_id)
    {
        if($u_id == session('id')) {
            $this->ajaxReturn(false);
        }

        // 删除前获取用户头像
        $imgData = M('detail')->where(['det_uid' => $u_id])->field('det_img')->find();
        $img = $imgData['det_img'];

        // 从user表删除
        if(M('user')->delete($u_id)) {
            // 从detail表删除
            M('detail')->where(['det_uid' => $u_id])->delete();
            
            if(!empty($img)) {
                unlink('./Uploads/'.$img);
            }
            $this->ajaxReturn(true);
        }else {
            $this->ajaxReturn(false);
        }
    }

    public function add()
    {
        $this->display();
    }

    public function insert()
    {
        if(I('post.u_password') !== I('post.u_password2')){
            $this->error('密码与确认密码不相同！',U('Admin/User/add'));
        }

        $userData['u_username'] = I('post.u_username');
        $userData['u_password'] = I('post.u_password');
        $user = D('User');
        if($user->create($userData,1)) {
            $u_id = $user->add();
            if($u_id) {
                $detailData['det_uid'] = $u_id;
                $detailData['det_name'] = I('post.u_username');
                $detailData['det_sex'] = I('post.det_sex');
                $detailData['det_tel'] = I('post.det_tel');
                $detailData['det_email'] = I('post.det_email');
                $detailData['det_introduce'] = I('post.det_introduce');

                $detail = D('detail');
                if(!$detail->create($detailData)) {
                    $user->delete($u_id);
                    $this->error($user->getError(),U('Admin/User/add'));
                }

                if(!$detail->add()) {
                    $this->error('添加失败！请稍后再试！',U('Admin/User/add'));
                }

                $this->success('添加成功',U('Admin/User/index'));
            } else {
                $this->error('添加失败！请稍后再试！',U('Admin/User/add'));
            }
        } else {
            $this->error($user->getError(),U('Admin/User/add'));
        }
    }
}