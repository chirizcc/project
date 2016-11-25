<?php
namespace Home\Controller;

use Think\Controller;

class CatalogController extends JudgeController
{
    private $size = 5;


    public function __construct()
    {
        parent::__construct();
        if (ACTION_NAME != 'index') {
            if (empty(session('b_id'))) {
                $this->redirect('Home/Authoredit/index');
            }
        }
    }
    // 目录首页
    public function index($b_id = null, $search = null)
    {

        $p = I('get.p/d');
        if ($p == null) {
            $p = 0;
        }
        // 判断数据是否为空，不是的话发送到session
        if (empty($b_id)) {
            if (empty(session('b_id'))) {
                $this->redirect('Home/Authoredit/index');
                die;
            } else {
                $b_id = session('b_id');
            }
        }

        // 搜索条件
        $map = [];
        if (!empty($search)) {
            $map['cata_name'] = ['like', '%' . $search . '%'];
        }
        // 查找数据
        $result = M('book')->where(['b_id' => $b_id])->field('b_name')->find();
        $name = $result['b_name'];
        // 发送数据到session
        session('b_id', $b_id);
        session('b_name', $name);
        // 查询数据库
        $data = M('catalog')->where(['cata_bid' => $b_id])->where($map)->order('cata_order')->page($p, $this->size)->select();
        // 查询满足要求的总记录数
        $count = M('catalog')->where(['cata_bid' => $b_id])->where($map)->count();
        $Page = new \Org\Util\MyPage($count, $this->size);// 实例化分页类 传入总记录数和每页显示的记录数

        if (!empty($search)) {
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                $Page->parameter[$key] = urlencode($val);
            }
        }

        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('data', $data);
        $this->display();
    }
    // 目录添加页
    public function add()
    {
        $this->display();
    }
    // 目录添加
    public function insert()
    {   //接收数据
        $catalog = M('catalog');
        $data = I('post.');

        if (empty(I('post.cata_name'))) {
            $this->error('章节名不能为空！');
            die;
        }
       
        $res = $catalog->where(['cata_bid' => session('b_id')])->max('cata_order');
        $order = $res + 1;
        $data['cata_order'] = $order;
        // 判断条件，成功则添加
        if ($catalog->create($data)) {
            if (($cata_id = $catalog->add()) > 0) {
                $data = ['con_catid' => $cata_id];
                $content = D('content');
                if ($content->create($data)) {
                    if ($content->add() > 0) {
                        $this->success('添加成功！', U('Home/Catalog/index', ['b_id' => session('b_id')]));
                    }
                } else {
                    $catalog->delete($cata_id);
                }
            }
        }

        $this->error('添加失败，请稍后再试！');
    }
    // 编辑页
    public function edit($cata_id = null)
    {
        if (empty($cata_id)) {
            $this->redirect('Home/Authoredit/index');
            die;
        }

        $data = M('catalog')->find($cata_id);
        $this->assign('data', $data);
        $this->display();
    }
    // 更新方法
    public function update()
    {
        $cata = M('catalog');
        // 判断条件，符合则成功
        if ($cata->create()) {
            if ($cata->save()) {
                $this->success('修改成功', U('Home/Catalog/index', ['b_id' => session('b_id')]));
            } else {
                $this->error('修改失败，请稍后再试！');
            }
        } else {
            $this->error($cata->getError());
        }
    }
    // 改变目录顺序
    public function changeOrder()
    {
        $catalog = M('catalog');
        // 返回ajax需要的数据
        if ($catalog->where(['cata_id' => I('post.cata_id')])->setField('cata_order', I('post.cata_order'))) {
            $this->ajaxReturn(true);
        }

        $this->ajaxReturn(false);
    }
    // 删除方法
    public function del($cata_id = null)
    {
        if (empty($cata_id)) {
            $this->ajaxReturn(false);
        }
        // 返回ajax需要的数据
        $cata = M('catalog');
        if ($cata->delete($cata_id)) {
            M('content')->where(['con_catid' => $cata_id])->delete();
            $this->ajaxReturn(true);
        } else {
            $this->ajaxReturn(false);
        }
    }
}