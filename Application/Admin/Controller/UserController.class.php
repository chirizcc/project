<?php

namespace Admin\Controller;

class UserController extends AdminController
{
    public function index($search = null)
    {
        $map = [];
        if(!empty($search)) {
            $map['u.u_username'] =  ['like','%'.$search.'%'];
        }

        $data = M('user')->table('zd_user as u,zd_detail as d')->where($map)->where('u.u_id = d.det_uid')->field('u.u_id id,u.u_username username,u.u_regtime time,u.u_istype type,d.det_name name,d.det_sex sex')->order('u.u_id desc')->page($_GET['p'],6)->select();

        $this->assign('data',$data);

        $count = M('user')->table('zd_user as u,zd_detail as d')->where($map)->where('u.u_id = d.det_uid')->count();// 查询满足要求的总记录数
        // 使用自定义分页类
        //$Page = new \Think\Page($count,1);
        $Page = new \Org\Util\MyPage($count,6);// 实例化分页类 传入总记录数和每页显示的记录数

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

}