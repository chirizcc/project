<?php
namespace Home\Controller;

use Think\Controller;

// 作者编辑
class AuthoreditController extends JudgeController
{
    private $size = 6;

    public function __construct()
    {
        parent::__construct();
        $type = M('user')->where('u_id = ' . session('home_id'))->field('u_istype')->find();
        if ($type['u_istype'] != 1) {
            $this->error('请先申请成为作者', U('Author/index'));
        }
    }

    public function index()
    {
        $p = I('get.p/d');
        if ($p == null) {
            $p = 0;
        }

        $map = [];
        if (!empty($search)) {
            $map['b_name'] = ['like', '%' . $search . '%'];
        }

        // 查询作者的小说
        $data = M('book')->where($map)->where('b_uid =' . session('home_id'))->order('b_id desc')->page($p, $this->size)->select();
        $count = M('book')->where($map)->where('b_uid =' . session('home_id'))->count();// 查询满足要求的总记录数


        $Page = new \Org\Util\MyPage($count, $this->size);

        if (!empty($search)) {
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                $Page->parameter[$key] = urlencode($val);
            }
        }

        $show = $Page->show();
        $this->assign('list', $data);
        $this->assign('page', $show);
        $this->display();

    }

    public function add()
    {
        $data = M('type')->where('t_pid=0')->order('t_id desc')->select();
        $this->assign('list', $data);
        $this->display();
    }

    // 书籍类别联动
    public function getadd()
    {
        $b_id = I('get.b_id/d');

        $data = M('type')->where('t_pid=' . $b_id)->select();
        $json_d = json_encode($data);
        $this->ajaxReturn($json_d);

        $this->assign('data', $data);
    }

    // 添加
    public function insert()
    {
        if (empty($_POST)) {
            $this->redirect('Home/Authoredit/add');
            exit;
        }

        if (I('post.b_tid') == 0 || I('post.b_tid') == null) {
            $this->error('请选择分类');
        }

        $bookModel = D('book');
        if ($bookModel->create()) {
            if ($bookModel->add() > 0) {
                $this->success('恭喜您,添加成功!', U('index'));
            } else {
                $this->error('添加失败....');
            }
        } else {
            $this->error($bookModel->getError());
        }
    }

    // 上传图片
    public function upload()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'bookimg'; // 设置附件上传（子）目录

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
            $image->thumb(300, 300)->save($path . 's_' . $name);

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
    
    public function edit()
    {
        $id = I('get.id/d');
        if (empty($id)) {
            $this->redirect('Home/Authoredit/index');
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
        $this->display('Authoredit/edit');
    }

    public function delImg($img = null)
    {
        if (empty($img)) {
            return;
        }

        $path = './Uploads/' . $img;
        unlink($path);
    }


    public function update()
    {
        if (empty($_POST)) {
            $this->redirect('Admin/User/add');
            exit;
        }

        if (I('post.b_tid') == 0 || I('post.b_tid') == null) {
            $this->error('请选择分类');
        }

        $bookModel = D('book');

        // 获取原来的图片
        $oldImg = $bookModel->where(['b_id' => I('post.b_id')])->field('b_img')->find();
        $oldImg = $oldImg['b_img'];

        if ($bookModel->create()) {
            //执行修改
            if ($bookModel->save() > 0) {
                // 如果有更改图片则删除旧图片
                if (I('post.b_img') != $oldImg) {
                    $this->delImg($oldImg);
                }
                $this->success('恭喜您,编辑成功!', U('index'));
            } else {
                if (I('post.b_img') != $oldImg) {
                    $this->delImg(I('post.b_img'));
                }
                $this->error('编辑失败....');
            }
        } else {
            if (I('post.b_img') != $oldImg) {
                $this->delImg(I('post.b_img'));
            }
            $this->error($bookModel->getError());
        }

    }
}