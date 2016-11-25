<?php
namespace Home\Controller;

// 收藏书架
class BookcaseController extends JudgeController
{
    public function index()
    {
        // 查询该用户收藏表
        $data = M('collect')->table('zd_collect c, zd_book b')->where('c.col_bookid=b.b_id and c.col_uid = ' . session('home_id'))->select();
        // 遍历
        // var_dump($data);die;


        // 发送数据
        $this->assign('data', $data);
        // 获取页面信息
        $this->display();

    }

    public function coladd()
    {

        // 判断传过来书籍id
        if (empty($_GET['b_id'])) {
            $this->redirect('index');
            exit;
        }
        // 判断该用户是否已经收藏
        $data = M('collect')->where("col_ui = " . session('home_id'))->where('col_bookid = ' . $_GET['b_id'])->select();
        if (!($data == null)) {
            die;
        }
        $col = [];
        // 过滤并获得书籍id
        $col['col_bookid'] = I('get.b_id/d');
        //获取用户id
        $col['col_uid'] = session('home_id');

        // 获取时间
        $col['col_time'] = time();

        M('collect')->create($col);
        if (M('collect')->add() > 0) {

            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(2);
        }

    }

    // 书架删除
    public function coldel()
    {
        if (empty($_GET['b_id'])) {
            $this->redirect('index');
            exit;
        }
        $id = I('get.b_id/d');

        if (M('collect')->where('col_uid = ' . session('home_id') . '  and col_bookid = ' . $id)->delete() > 0) {
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(2);
        }

    }
}