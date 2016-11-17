<?php
namespace Home\Controller;
use Think\Controller;

class DetailsController extends Controller
{
    public function index($b_id = null){
        if(empty($b_id)) {
            $this->redirect('Home/Index/index');
        }
        
        $data = M('book')->find($b_id);
        if(empty($data)) {
            $this->redirect('Home/Index/index');
        }
        $this->assign('data',$data);
        $this->display();
    }
}