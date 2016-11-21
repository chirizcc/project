<?php
namespace Home\Controller;

class TypeController extends HomeController
{
    public function index()
    {
        $t_pid = I('get.t_pid');
        $search = I('get.search');
        $t_id = I('get.t_id');

        $data = [];
        if (!empty($t_pid)) {
            $crumb[] = D('type')->where(['t_id' => $t_pid])->field('t_name')->find();
            $childType = D('type')->where(['t_pid' => $t_pid])->field('t_id')->select();
            foreach ($childType as $v) {
                $books = M('book')->where(['b_tid' => $v['t_id'], 'b_status' => 1])->field('b_id, b_name, b_img, b_author')->select();
                foreach ($books as $value) {
                    $data[] = $value;
                }
            }
        } elseif(!empty($t_id)){
            $parentType = D('type')->where(['t_id' => $t_id])->field('t_pid')->find();
            $crumb[] = D('type')->where(['t_id' => $parentType['t_pid']])->field('t_name,t_id')->find();
            $crumb[] = D('type')->where(['t_id' => $t_id])->field('t_name,t_id')->find();
            $data = M('book')->where(['b_tid' => $t_id, 'b_status' => 1])->field('b_id, b_name, b_img, b_author')->select();
        } elseif (!empty($search)) {

        } else {
            $this->redirect('Home/index/index');
        }

        $types = D('type')->getTypes();
        $this->assign('crumb', $crumb);
        $this->assign('data', $data);
        $this->assign('types', $types);
        $this->display();
    }
}