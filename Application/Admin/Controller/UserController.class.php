<?php

namespace Admin\Controller;

class UserController extends AdminController
{
    public function index()
    {
        
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
        if(false === $user->where('')->save($data)) {
            echo $user->getLastSql();
            die;
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

}