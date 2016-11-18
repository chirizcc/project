<?php
namespace Home\Controller;

class DetailsController extends HomeController
{
    private $commentSize = 5;
    
    public function index($b_id = null){
        if(empty($b_id)) {
            $this->redirect('Home/Index/index');
        }
        
        $data = M('book')->where(['b_status' => 1,'b_id' => $b_id])->field('b_id,b_name,b_tid,b_img,b_click,b_introduce,b_author')->find();
        if(empty($data)) {
            $this->error('该书已下架');
        }

        // 获取该书的分类
        $res = M('type')->field('t_name,t_pid')->find($data['b_tid']);
        $data['t_name'] = $res['t_name'];
        $res = M('type')->field('t_name')->find($res['t_pid']);
        $data['t_pname'] = $res['t_name'];

        // 获取该书的目录
        $catalog = M('catalog')->where(['cata_bid' => $b_id,'cata_status' => 1])->field('cata_id,cata_name')->order('cata_order')->select();
        
        $cataCount = M('catalog')->where(['cata_bid' => $b_id,'cata_status' => 1])->count();
        $data['cataCount'] = $cataCount;

        // 获取最后更新时间
        $lastTime = M('catalog')->table('zd_catalog ca,zd_content co')->where('ca.cata_id = co.con_catid and ca.cata_status = 1 and ca.cata_bid = '.$b_id)->max('co.con_time');
        $data['lastTime'] = empty($lastTime) ? '无' :date('Y-m-d H:i',$lastTime);
        
        $this->assign('comment', $this->getComment($b_id, I('get.p')));
        $this->assign('catalog',$catalog);
        $this->assign('data',$data);
        $this->display();
    }

    public function getComment($b_id, $p = 0)
    {
        $data = M('comment')->table('zd_comment c,zd_detail d')->where('c.com_uid = d.det_uid and c.com_bid = '.$b_id)->field('d.det_name,d.det_img,com_content,com_time')->order('c.com_id desc')->page($p, $this->commentSize)->select();

        $count = M('comment')->table('zd_comment c,zd_detail d')->where('c.com_uid = d.det_uid and c.com_bid = '.$b_id)->count();// 查询满足要求的总记录数
        $Page = new \Org\Util\MyPage($count, $this->commentSize);// 实例化分页类 传入总记录数和每页显示的记录数

        $Page->anchor = '#comment';
        /*dump($Page->anchor);
        die;*/
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        
        return $data;
    }
}