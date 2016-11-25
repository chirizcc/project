<?php

namespace Admin\Controller;

// 书籍审核控制器
class ExamineController extends AdminController
{
    //书籍审核
    public function index($search = null)
    {
        $book = M('book');

        $map = [];
        if (!empty($search)) {
            $map['b_name'] = ['like', '%' . $search . '%'];
            // var_dump($map);exit;
        }
        $map['b_status'] = array('not in','1,3');
        //分页
        $count = $book->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Org\Util\MyPage($count, 3);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $data = $book->order('b_id desc')->where($map)->page($_GET['p'], '3')->select();
        // var_dump($data);exit;

        if (!empty($search)) {
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                $Page->parameter[$key] = urlencode($val);
            }
        }

        $this->assign('page', $show);// 赋值分页输出
        $this->assign('data', $data);
        $this->display();
    }

    //书籍审核通过
    public function allow()
    {
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Examine/index');
            exit;
        }
        //接收参数
        $id = I('get.id/d');

        $book = M('book');
        //拼接条件
        $map = [];
        $map['b_id'] = $id;
        //拼接修改字段
        $where = [];
        $where['b_status'] = '1';
        // var_dump($book->where($map)->find());exit;
        if ($book->where($map)->save($where) !== false) {
            //给作者发消息，通知书籍状态
            $bookinfo = $book->where($map)->find();//查出此本书的信息
            //拼接信息
            $message = "您申请的书籍".$bookinfo['b_name']."审核已通过";
            $info = [];
            $info['i_uid'] = $bookinfo['b_uid'];
            $info['i_info'] = $message;
            $info['i_time'] = time();
            //将消息发给作者
            if(M('info')->add($info)){
                $this->success('恭喜您,审核通过成功!', U('index'));
            }else{
                $this->error('通过审核失败');
            }           
        } else {
            $this->error('通过审核失败');
        }

    }

    //书籍审核不通过
    public function forbid()
    {
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Examine/index');
            exit;
        }
        //接收参数
        $id = I('get.id/d');

        $book = M('book');
        //拼接条件
        $map = [];
        $map['b_id'] = $id;
        //拼接修改字段
        $where = [];
        $where['b_status'] = '3';
        if ($book->where($map)->save($where) !== false) {
            // $this->success('下架书本成功', U('index'));
            //给作者发消息，通知书籍状态
            $bookinfo = $book->where($map)->find();//查出此本书的信息
            //拼接信息
            $message = "您申请的书籍".$bookinfo['b_name']."审核不通过，具体原因请联系管理员";
            $info = [];
            $info['i_uid'] = $bookinfo['b_uid'];
            $info['i_info'] = $message;
            $info['i_time'] = time();
            //将消息发给作者
            if(M('info')->add($info)){
                $this->success($bookinfo['b_name'].'审核不通过!', U('index'));
            }else{
                $this->error('通过审核失败');
            } 
        } else {
            $this->error('下架书本失败');
        }
    }

    //作者审核
    public function author()
    {
        $user = M('user');
        $map = [];
        $map['u_istype'] = 2;
        //分页
        $count = $user->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Org\Util\MyPage($count, 6);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $data = $user->order('u_id desc')->where($map)->page($_GET['p'], '6')->select();
        // 获取当前时间
        $time = time();
        // var_dump($data[0]['u_regtime']);exit();
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('time', $time);// 时间输出
        $this->assign('data', $data);
        $this->display();
    }

    //作者审核通过
    public function autallow()
    {
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Examine/author');
            exit;
        }
        //接收参数
        $id = I('get.id/d');

        $user = M('user');
        //拼接条件
        $map = [];
        $map['u_id'] = $id;
        //拼接修改字段
        $where = [];
        $where['u_istype'] = '1';
        if ($user->where($map)->save($where) !== false) {
            //给作者发消息，通知审核状态
            //拼接信息
            $message = "您申请成为作者，审核已通过";
            $info = [];
            $info['i_uid'] = $id;
            $info['i_info'] = $message;
            $info['i_time'] = time();
            //将消息发给作者
            if(M('info')->add($info)){
                $this->success('恭喜您,审核通过成功!', U('Examine/author'));
            }else{
                $this->error('通过审核失败');
            } 
        } else {
            $this->error('通过审核失败');
        }

    }

    //作者审核不通过
    public function autforbid()
    {
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Examine/author');
            exit;
        }
        //接收参数
        $id = I('get.id/d');

        $user = M('user');
        //拼接条件
        $map = [];
        $map['u_id'] = $id;
        //拼接修改字段
        $where = [];
        $where['u_istype'] = '3';
        if ($user->where($map)->save($where) !== false) {
            //给作者发消息，通知审核状态
            //拼接信息
            $message = "您申请成为作者失败";
            $info = [];
            $info['i_uid'] = $id;
            $info['i_info'] = $message;
            $info['i_time'] = time();
            //将消息发给作者
            if(M('info')->add($info)){
                $this->success('恭喜您,审核通过成功!', U('Examine/author'));
            }else{
                $this->error('拒绝通过失败，请重新拒绝');
            } 
        } else {
            $this->error('拒绝通过失败，请重新拒绝');
        }
    }

    //内容审核
    public function content($search = null)
    {
        $cat = M('catalog');
        $map = [];
        if (!empty($search)) {
            $map['b.b_name'] = ['like', '%' . $search . '%'];
            // var_dump($map);exit;
        }
        $map['c.cata_status'] = '0';
        $map['b.b_status'] = '1';

        //分页
        $count = $cat->table('zd_catalog c,zd_book b')->where($map)->where('c.cata_bid = b.b_id')->count();
        // var_dump($count);exit;
        $Page = new \Org\Util\MyPage($count, 5);// 实例化分页类 传入总记录数和每页显示的记录数

        $show = $Page->show();// 分页显示输出
        $data = $cat->table('zd_catalog c,zd_book b')->where($map)->where('c.cata_bid = b.b_id')->field('c.cata_id id,b.b_name bookname,c.cata_name name,b.b_author author')->page($_GET['p'], '5')->select();
        if (!empty($search)) {
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                $Page->parameter[$key] = urlencode($val);
            }
        }
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('data', $data);
        $this->display();
    }

    //内容审核通过
    public function contAllow()
    {
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Examine/content');
            exit;
        }
        //接收参数
        $id = I('get.id/d');

        $catalog = M('catalog');
        //拼接条件
        $map = [];
        $map['cata_id'] = $id;
        //拼接修改字段
        $where = [];
        $where['cata_status'] = '1';
        if ($catalog->where($map)->save($where) !== false) {
            // $this->success('恭喜您,审核通过!', U('Examine/content'));
            //给作者发消息，通知审核状态
            //拼接信息
            $cat = M('catalog');
            $maps = [];
            $maps['c.cata_id'] = $id;
            $data = $cat->table('zd_catalog c,zd_book b')->where($maps)->where('c.cata_bid = b.b_id')->find();
            // var_dump($data);exit;
            $message = "您的书籍".$data['b_name']."的".$data['cata_name']."审核通过";
            $readerMessage = "您收藏的书籍".$data['b_name']."发布新章节了，赶紧去看看吧";
            $info = [];
            $info['i_uid'] = $data['b_uid'];
            $info['i_info'] = $message;
            $info['i_time'] = time();
            //将消息发送给订阅的读者
            // $collecter = M('collect')->
            //将消息发给作者
            if(M('info')->add($info)){
                $this->success('恭喜您,审核通过成功!', U('Examine/content'));
            }else{
                $this->error('拒绝通过失败，请重新拒绝');
            } 
        } else {
            $this->error('通过审核失败');
        }

    }

    //内容审核不通过
    public function contForbid()
    {
        //判断有无传递ID
        if (empty($_GET['id'])) {
            $this->redirect('Admin/Examine/content');
            exit;
        }
        //接收参数
        $id = I('get.id/d');

        $catalog = M('catalog');
        //拼接条件
        $map = [];
        $map['cata_id'] = $id;
        //拼接修改字段
        $where = [];
        $where['cata_status'] = '2';
        if ($catalog->where($map)->save($where) !== false) {
            // $this->success('拒绝通过成功', U('Examine/content'));
            //给作者发消息，通知审核状态
            //拼接信息
            $cat = M('catalog');
            $maps = [];
            $maps['c.cata_id'] = $id;
            $data = $cat->table('zd_catalog c,zd_book b')->where($maps)->where('c.cata_bid = b.b_id')->find();
            // var_dump($data);exit;
            $message = "您的书籍".$data['b_name']."的".$data['cata_name']."审核不通过，具体原因请联系管理员";
            $info = [];
            $info['i_uid'] = $data['b_uid'];
            $info['i_info'] = $message;
            $info['i_time'] = time();
            //将消息发给作者
            if(M('info')->add($info)){
                $this->success('恭喜您,拒绝通过成功!', U('Examine/content'));
            }else{
                $this->error('拒绝通过失败，请重新拒绝');
            } 
        } else {
            $this->error('拒绝通过失败，请重新拒绝');
        }
    }

}