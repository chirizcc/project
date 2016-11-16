<?php

namespace Admin\Controller;


class CatalogController extends AdminController
{
    private $b_id = null;

    public function __construct()
    {
        parent::__construct();
        if(CONTROLLER_NAME != 'index') {
            $this->b_id = session('b_id');
            if (empty($this->b_id)) {
                $this->redirect('Admin/Book/index');
            }
        }
    }

    public function index($b_id = null)
    {
        if(empty($b_id)) {
            $this->redirect('Admin/Book/index');
            die;
        }
        
        $result = M('book')->where(['b_id' => $this->b_id])->field('b_name')->find();
        $name = $result['b_name'];
        
        session('b_id',$b_id);
        session('b_name',$name);
        
        $data = M('catalog')->where(['cata_bid' => $b_id])->order('cata_order')->select();
        $this->assign('data',$data);
        $this->display();
    }

    public function add()
    {
        $this->display();
    }
    
    public function insert()
    {
        $catalog = M('catalog');
        $data = I('post.');

        $res = $catalog->where(['cata_bid' => session('b_id')])->max('cata_order');
        $order = $res + 1;
        $data['cata_order'] = $order;

        if($catalog->create($data)) {
            if(($cata_id = $catalog->add()) > 0) {
                $data = ['con_catid' => $cata_id];
                $content = D('content');
                if($content->create($data)) {
                    if($content->add() > 0) {
                        $this->success('添加成功！',U('Admin/Catalog/index',['b_id' => session('b_id')]));
                    }
                } else {
                    $catalog->delete($cata_id);
                }
            }
        }

        $this->error('添加失败，请稍后再试！');
    }
    
    public function changeOrder()
    {
        $catalog = M('catalog');

        if($catalog->where(['cata_id' => I('post.cata_id')])->setField('cata_order',I('post.cata_order'))) {
            $this->ajaxReturn(true);
        }

        $this->ajaxReturn(false);
    }
}