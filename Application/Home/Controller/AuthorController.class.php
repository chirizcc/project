<?php
namespace Home\Controller;

use Think\Controller;

// 申请成为作者
class AuthorController extends JudgeController
{
    public function index()
    {
        // 查询用户详情
        $data = M('detail')->where('det_uid = ' . session('home_id'))->select();
        // 发送数据
        $this->assign('data', $data[0]);
        $book = M('book')->where('b_uid = ' . session('home_id'))->select();
        $this->assign('book', $book[0]);
        $this->display();
    }

    public function add()
    {
        if (empty($_POST)) {
            $this->redirect('index');
            exit;
        }
        $det = D('detail');

        // 判断姓名是否为空
        if ($_POST['det_name'] == null) {
            $this->error('姓名不能为空,请去修改', U('Home/Center/info'));
            exit;
        }

        // 判断手机是否为空
        if ($_POST['det_tel'] == null) {
            $this->error('手机不能为空,请去修改', U('Home/Center/info'));
            exit;
        }


        // 判断邮箱是否为空
        if ($_POST['det_email'] == null) {
            $this->error('邮箱不能为空,请去修改', U('Home/Center/info'));
            exit;
        }

        // 判断笔名是否为空


        $data = $det->where('det_id =' . session('home_id'))->save($_POST);

        $user = M('user')->where('u_id = ' . session('home_id'))->save($_POST);
        if ($data || $user) {
            $this->success('已成功提交申请', U('index'));
        } else {
            $this->error('提交失败', U('index'));
        }
    }
}