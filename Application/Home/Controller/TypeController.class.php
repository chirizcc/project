<?php
namespace Home\Controller;

class TypeController extends HomeController
{
    public function index()
    {
        $t_pid = I('get.t_pid');
        $search = I('post.search');
        $t_id = I('get.t_id');

        $data = [];
        if (!empty($t_pid)) {
            // 点击顶级分类进入的分类页
            $crumb[] = D('type')->where(['t_id' => $t_pid])->field('t_name')->find();
            $childType = D('type')->where(['t_pid' => $t_pid])->field('t_id')->select();
            foreach ($childType as $v) {
                $books = M('book')->where(['b_tid' => $v['t_id'], 'b_status' => 1])->field('b_id, b_name, b_img, b_author')->select();
                foreach ($books as $value) {
                    $data[] = $value;
                }
            }
            $parentId = $t_pid;
        } elseif (!empty($t_id)) {
            // 点击具体的2级分类进入的分类页
            $parentType = D('type')->where(['t_id' => $t_id])->field('t_pid')->find();
            $crumb[] = D('type')->where(['t_id' => $parentType['t_pid']])->field('t_name,t_id')->find();
            $crumb[] = D('type')->where(['t_id' => $t_id])->field('t_name,t_id')->find();
            $data = M('book')->where(['b_tid' => $t_id, 'b_status' => 1])->field('b_id, b_name, b_img, b_author')->select();
            $parentId = $parentType['t_pid'];
        } elseif (!empty($search)) {
            // 通过搜索进入分类页
            $crumb[] = ['t_name' => '搜索结果'];
            $map['b_name'] = ['like', '%'.$search.'%'];
            $data = M('book')->where(['b_status' => 1])->where($map)->select();
        } else {
            $this->redirect('Home/index/index');
        }

        $types = D('type')->getTypes();

        // 默认展开的分类导航
        $this->assign('parent', $parentId);
        $this->assign('crumb', $crumb);
        $this->assign('data', $data);
        $this->assign('types', $types);
        $this->display();
    }
}