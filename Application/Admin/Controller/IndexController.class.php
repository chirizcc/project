<?php
namespace Admin\Controller;

use Think\Controller;

class IndexController extends AdminController
{
    public function index()
    {
        $u_id = session('id');
        $img = M('user')->table('zd_user as u,zd_detail as d')->where('u.u_id = d.det_uid and u.u_id = %d',$u_id)->field('det_img as img')->find();
        $this->assign('img',$img['img']);
     	$this->display();
    }
}