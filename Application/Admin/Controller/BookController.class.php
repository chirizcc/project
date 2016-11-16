<?php
namespace Admin\Controller;

use Think\Controller;

class BookController extends AdminController
{
    public function index()
    {
        $p = $_GET['p'];
        if ($p == null) {
            $p = 0;
        }
        $data = M('book')->order('b_id desc')->page($p, 6)->select();
        $this->assign('list', $data);
        $count = M('book')->count();

        $Page = new \Org\Util\MyPage($count, 6);
        $show = $Page->show();

        $this->assign('page', $show);
        $this->display();
    }

    public function del()
    {

        if (empty($_GET['id'])) {
            $this->redirect('Admin/Book/index');
            exit;
        }

        $id = I('get.id/d');
        $status['b_status'] = 2;
        if (M('book')->where('b_id=' . $id)->save($status) > 0) {
            $this->success('恭喜您,下架成功!', U('index'));
        } else {
            $this->error('下架失败....', U('index'));
        }
    }

    //添加页面
    public function add()
    {

        /*$this->assign('title1', '书籍管理');
        $this->assign('title2', '填写书籍内容');*/
        $data = M('type')->where('t_pid=0')->order('t_id desc')->select();
        $this->assign('list', $data);
        /*$value = session();
        $this->assign('value', $value);*/
//        $this->assign('title', '添加用户');
        $this->display('Book/add');


    }

    public function getadd()
    {
        $b_id = I('get.b_id/d');

        $data = M('type')->where('t_pid=' . $b_id)->select();
        $json_d = json_encode($data);
        $this->ajaxReturn($json_d);

        $this->assign('data', $data);
    }

    public function insert()
    {
        /*$upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'bookimg'; // 设置附件上传（子）目录
        // 上传文件
        $info = $upload->upload();

        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            $this->success('上传成功！', U('index'));
        }
        $path = $info['b_img']['savepath'];
        $name = $info['b_img']['savename'];
        $img['b_img'] = $path . $name;*/

        if (empty($_POST)) {
            $this->redirect('Admin/Book/add');
            exit;
        }

        if(I('post.b_tid') == 0 || I('post.b_tid') == null) {
            $this->error('请选择分类');
        }

        /*dump(D('book')->create());
        die;*/
        $bookModel = D('book');
        if($bookModel->create()) {
            if ($bookModel->add() > 0) {
                /*$book = M('book')->order('b_id desc')->select();
                $b_id = $book[0]['b_id'];
                M('book')->where('b_id=' . $b_id)->save($img);*/
                $this->success('恭喜您,添加成功!', U('index'));
            } else {
                $this->error('添加失败....');
            }
        } else {
            $this->error($bookModel->getError());
        }
    }

    public function edit()
    {
        $id = I('get.id/d');
        if(empty($id)) {
            $this->redirect('Admin/Book/index');
            die;
        }

        $type = M('type')->where('t_pid=0')->order('t_id desc')->select();
        $this->assign('list', $type);
        // 接收id

        //查找
        $data = M('book')->find($id);
        $this->assign('data', $data);
        // 子分类
        $type2 = M('type')->where('t_id=' . $data['b_tid'])->select();
        $this->assign('type2', $type2);
        // 顶级分类
        $type3 = M('type')->where('t_id=' . $type2[0]['t_pid'])->select();
        $this->assign('type3', $type3);
        /*$this->assign('title1', '书籍修改');
        $this->assign('title2', '修改书籍内容');*/
        $this->display('Book/edit');
        // echo M('type')->getLastSql().'<br>';
    }

    public function upload() 
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'bookimg'; // 设置附件上传（子）目录

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
            $image->thumb(300, 300)->save($path.'s_'.$name);

            //删除原图片
            unlink($path.$name);

            $data = [
                'code' => 0,
                'msg' => '上传成功',
                'url' => $info['file']['savepath'] . 's_' . $name,
            ];
        }
        $this->ajaxReturn($data);
    }

    // 点击重置按钮时删除图片
    public function delImg($img = null)
    {
        if(empty($img)) {
            return;
        }

        $path = './Uploads/'.$img;
        unlink($path);
    }


    public function update()
    {
        if (empty($_POST)) {
            $this->redirect('Admin/User/add');
            exit;
        }

        if(I('post.b_tid') == 0 || I('post.b_tid') == null) {
            $this->error('请选择分类');
        }

        $bookModel = D('book');

        // 获取原来的图片
        $oldImg = $bookModel->where(['b_id' => I('post.b_id')])->field('b_img')->find();
        $oldImg = $oldImg['b_img'];

        if($bookModel->create()) {
            //执行修改
            if ($bookModel->save() > 0) {
                // 如果有更改图片则删除旧图片
                if(I('post.b_img') != $oldImg) {
                    $this->delImg($oldImg);
                }
                $this->success('恭喜您,编辑成功!');
            } else {
                if(I('post.b_img') != $oldImg) {
                    $this->delImg(I('post.b_img'));
                }
                $this->error('编辑失败....');
            }
        } else {
            if(I('post.b_img') != $oldImg) {
                $this->delImg(I('post.b_img'));
            }
            $this->error($bookModel->getError());
        }

    }

    public function dir()
    {
        // 接收id
        $id = I('get.id/d');
        session('bid', $id);
        //查找
        $book = M('book')->find($id);
        $dir = M('catalog')->where('cata_bid=' . $id)->order('cata_order desc')->select();

        // session('dir',$dir[0]['cata_id']);
        $this->assign('dir', $dir);
        $this->assign('title1', $book['b_name']);
        $this->assign('id', $id);
        $this->display('Book/dir');
    }


    public function diradd()
    {
        $id = I('get.id/d');
        $this->assign('id', $id);

        $this->display('Book/diradd');
        // echo M('catalog')->getLastSql().'<br>';

    }

    public function addinsert()
    {
        if (empty($_POST)) {
            $this->redirect('Admin/Book/diradd');
            exit;
        }
        M('catalog')->create($_POST);

        if (M('catalog')->add() > 0) {
            $value = M('catalog')->order('cata_id desc')->select();
            $data['con_time'] = time();
            M('content')->add($data);
            $content = M('content')->order('con_id desc')->select();
            $cata['cata_cid'] = $content[0]['con_id'];
            M('catalog')->where('cata_id=' . $value[0]['cata_id'])->save($cata);


            $this->success('恭喜您,编辑成功!', U('dir', array('id' => session('bid'))));
        } else {
            $this->error('编辑失败....', U('dir', array('id' => session('bid'))));
        }


    }

    public function dirdel()
    {
        if (empty($_POST)) {
            $this->redirect('Admin/Book/dir');
            exit;
        }


        if (M('catalog')->where("cata_bid=" . session('bid') . " and  cata_order=" . $_POST['cata_order'] . "")->delete() > 0) {
            $this->success('恭喜您,删除成功!', U('dir', array('id' => session('bid'))));
        } else {
            $this->error('删除失败....', U('dir', array('id' => session('bid'))));
        }
    }


    public function delajax()
    {
        $cata_order = I('get.cata_order/d');

        $data = M('catalog')->where("cata_bid=" . session('bid') . " and cata_order=" . $cata_order . "")->select();
        if (!$data) {
            $json_d = json_encode('没有该章节');

        } else {
            $json_d = json_encode('√');
        }
        $this->ajaxReturn($json_d);

    }


    public function content()
    {
        $id = I('get.id/d');
        session('dir', $id);
        $value = M('catalog')->where('cata_id=' . $id)->select();


        $con = M('content')->where('con_id=' . $value[0]['cata_cid'])->select();

        $this->assign('id', $value[0]['cata_cid']);
        $this->assign('name', $value[0]['cata_name']);
        $this->assign('con', $con);
        $this->display('Book/content');

    }

    public function contentadd()
    {
        $id = I('get.id/d');

        $this->assign('id', $id);

        $this->display('Book/contentadd');
    }

    public function conupdate()
    {
        $id = I('get.id/d');

        $content['con_content'] = $_POST['con_content'];
        $content['con_time'] = $_POST['con_time'];
        if (M('content')->where('con_id=' . $id)->save($content) > 0) {
            $this->success('恭喜您,添加成功!', U('content', array('id' => session('dir'))));
        } else {
            $this->error('添加失败', U('content', array('id' => session('dir'))));
        }


    }

    public function img()
    {
        $id = I('get.id/d');
        $book = M('book')->where('b_id=' . $id)->select();
        $this->assign('img', $book[0]['b_img']);
        $this->display();
    }

}