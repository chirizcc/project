<?php
namespace Home\Controller;
use Think\Controller;

//阅读页面控制器
class ReadController extends HomeController
{
	public function index($cata_id = null)
	{
		if(empty($cata_id)) {
            $this->error('该章节不存在');
		}

        // 获取章节信息
        $cataData = M('catalog')->where(['cata_status' => 1,'cata_id' => $cata_id])->field('cata_bid,cata_name')->find();
        if(empty($cataData)) {
            $this->error('该章节不存在');
        }

        $data = $cataData;

        // 获取书籍信息
        $bookData = M('book')->where(['b_status' => 1,'b_id' => $cataData['cata_bid']])->field('b_name,b_author,b_tid,b_id')->find();
        if(empty($bookData)) {
            $this->error('该书已下架');
        }

        foreach ($bookData as $key => $value) {
            $data[$key] = $value;
        }

        // 获取该书的分类
        $res = M('type')->field('t_name,t_pid')->find($data['b_tid']);
        $data['t_name'] = $res['t_name'];
        $res = M('type')->field('t_name')->find($res['t_pid']);
        $data['t_pname'] = $res['t_name'];

        // 获取章节内容
        $content = M('content')->where(['con_catid' => $cata_id])->find();
        if(empty($content)) {
            $this->error('该章节不存在');
        }

        // 统计字数
        $encode = 'UTF-8';
        $num = mb_strlen(strip_tags($content['con_content']),$encode);
        
        $content['num'] = $num;

        // 获取目录
        $catalog = M('catalog')->where(['cata_bid' => $data['cata_bid'],'cata_status' => 1])->field('cata_id,cata_name')->order('cata_order')->select();

        // 拼接所有目录
        $str = '';
        foreach ($catalog as $v) {
            if($v['cata_id'] == $cata_id) {
                $str.= '<li style="margin-top: 3px"><a href="'.U('Home/Read/index',['cata_id' => $v['cata_id']]).'" class="btn btn-info">'.$v['cata_name'].'</a></li>';
            } else {
                $str.= '<li style="margin-top: 3px"><a href="'.U('Home/Read/index',['cata_id' => $v['cata_id']]).'" class="btn btn-default">'.$v['cata_name'].'</a></li>';
            }
        }

        $num = 0;
        foreach ($catalog as $k => $v) {
            if($cata_id == $v['cata_id']) {
                $num = $k;
            }
        }
        
        // 获取上一个章节
        if($num == 0) {
            $last = '<a href="#" class="btn btn-default" disabled>已是第一章</a>';
        } else {
            $last = '<a href="'.U('Home/Read/index',['cata_id' => $catalog[$num - 1]['cata_id']]).'" class="btn btn-default">上一章</a>';
        }

        // 获取下一个章节
        if($num == (count($catalog) - 1)) {
            $next = '<a href="#" class="btn btn-default" disabled>已是最后一章</a>';
        } else {
            $next = '<a href="'.U('Home/Read/index',['cata_id' => $catalog[$num + 1]['cata_id']]).'" class="btn btn-default">下一章</a>';
        }

        //增加浏览历史
        //判断是否有登录
        if(!empty(session('home_id'))){
            $bookid = $bookData['b_id'];
            //判断是否已经添加过,添加过的话，更新最后浏览时间
            $map = [];
            $map['h_bid'] = $bookid;
            $map['h_uid'] = session('home_id');
            $history = M('history')->where($map)->select();
            if(empty($history)){
                $map['h_time'] = time();
                M('history')->add($map);
            }else{
                 $maps['h_time'] = time();
                 $maps['h_cata'] = $cata_id;
                 $hid = $history[0]['h_id'];
                 // var_dump($hid);exit;
                 $where = [];
                 $where['h_id'] = $hid;
                 M('history')->where($where)->save($maps);
            }
        }
        // exit;


        $this->assign('last',$last);
        $this->assign('next',$next);
        $this->assign('catalog',$str);
        $this->assign('content',$content);
        $this->assign('data',$data);
		$this->display();


	}
}