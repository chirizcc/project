<?php

namespace Home\Controller;

// 个人中心控制器
class CenterController extends JudgeController
{
    public function index()
    {
        $u_id = session('home_id');
        $img = M('detail')->where(['det_uid' => $u_id])->field('det_img')->find();
        $data = M('user')->where('u_id = ' . session('home_id'))->field('u_istype')->select();
        $this->assign('img', $img['det_img']);
        $this->assign('data', $data);
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
        $data['regTime'] = date('Y-m-d H:i', $res['u_regtime']);

        $this->assign('data', $data);
        $this->display();
    }

    public function edit()
    {
        $u_id = session('home_id');
        $data = M('detail')->where(['det_uid' => $u_id])->find();
        $this->assign('data', $data);
        $this->display();
    }

    public function update()
    {
        $de = D('detail');
        if ($de->create()) {
            if (false === $de->save()) {
                $this->error('编辑失败，请稍后再试!');
            } else {
                $this->success('编辑成功', U('Home/Center/info'));
            }
        } else {
            $this->error($de->getError());
        }
    }

    public function uploadPortrait()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'Portrait'; // 设置附件上传（子）目录
// 上传文件
        $info = $upload->upload();

        if (!$info) {// 上传错误提示错误信息
            $data = [
                'code' => 1,
                'msg' => $upload->getError(),
            ];
        } else {
            $path = './Uploads/' . $info['file']['savepath'];
            $name = $info['file']['savename'];

            $image = new \Think\Image();
            $image->open($path . $name);
            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
            $image->thumb(200, 200)->save($path . 's_' . $name);

            //删除原图片
            unlink($path . $name);

            $data = [
                'code' => 0,
                'msg' => '上传成功',
                'url' => $info['file']['savepath'] . 's_' . $name,
            ];
        }

        $this->ajaxReturn($data);
    }

    public function insertPortrait()
    {
        $data = [
            'code' => 1,
            'msg' => '',
        ];
        $url = I('post.url');
        if (is_file('./Uploads/' . $url)) {
            $u_id = session('home_id');
            $res = M('detail')->where(['det_uid' => $u_id])->field('det_id,det_img')->find();
            if (M('detail')->create(['det_id' => $res['det_id'], 'det_img' => $url])) {
                if (M('detail')->save()) {
                    $data['code'] = 0;
                    $data['msg'] = '头像保存成功';
                    unlink('./Uploads/' . $res['det_img']);
                } else {
                    unlink('./Uploads/' . $url);
                    $data['msg'] = '头像保存失败，请稍后再试!';
                }
            } else {
                unlink('./Uploads/' . $url);
                $data['msg'] = M('detail')->getError();
            }
        } else {
            $data['msg'] = '头像保存失败，请稍后再试!';
        }
        $this->ajaxReturn($data);
    }

    // 点击重置按钮时删除图片
    public function delImg()
    {
        $url = I('post.url');
        if (empty($url)) {
            return;
        }

        $path = './Uploads/' . $url;
        unlink($path);
    }

    public function changeEmail()
    {
        $data = [
            'code' => 1,
            'msg' => '',
        ];
        $email = I('post.email');
        $res = M('detail')->where(['det_uid' => session('home_id')])->field('det_email')->find();
        $oldEmail = $res['det_email'];

        if ($email == $oldEmail) {
            $data['msg'] = '新邮箱与旧邮箱相同，无需修改！';
            $this->ajaxReturn($data);
        }

        $wait = D('wait');
        $wData = ['w_uid' => session('home_id'), 'w_email' => $email, 'w_status' => 1];
        if ($wait->create($wData)) {
            $wid = $wait->add();
            if ($wid) {
                $title = '更改邮箱验证';
                // 发送到邮箱的链接采用base64加密
                $content = '尊敬的用户' . session('home_name') . ': 您正在更改你的邮箱，您可以通过点击以下链接完成操作: <a href="' . U('Home/Center/activa', ['w_id' => base64_encode($wid)], 'html', true) . '">' . U('Home/Center/activa', ['w_id' => base64_encode($wid)], 'html', true) . '</a>';
                if ($this->sendEmail($email, $title, $content)) {
                    $data['code'] = 0;
                    $data['msg'] = '邮件发送成功!请前往您的邮箱完成后面的操作!';
                } else {
                    $data['msg'] = '邮件发送失败，请稍后再试！';
                }
            } else {
                $data['msg'] = '邮件发送失败，请稍后再试！';
            }
        } else {
            $data['msg'] = '邮件发送失败，请稍后再试！';
        }

        $this->ajaxReturn($data);
    }

    /**
     * 修改邮箱时邮箱里的链接处理
     * @param $w_id wait表的主键
     */
    public function activa($w_id = null)
    {
        if(empty($w_id)) {
            $this->redirect('Home/Index/index');
        }

        $w_id =  base64_decode($w_id);
        $res = D('wait')->where(['w_id' => $w_id, 'w_status' => 1])->find();
        if(!$res) {
            $this->error('验证出错！', U('Home/Index/index'));
        }

        $num = D('detail')->where(['det_uid' => $res['w_uid']])->save(['det_email' => $res['w_email']]);
        D('wait')->where(['w_id' => $w_id])->delete();

        if($num) {
            $this->success('修改邮箱成功!', U('Home/Center/index'));
        } else {
            $this->success('修改邮箱失败!请稍后再试', U('Home/Center/index'));
        }

    }
}