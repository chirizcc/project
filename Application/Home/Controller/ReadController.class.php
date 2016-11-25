<?php
/**
 * 阅读页面控制器
 * author 张德昌 沈悦群
 */
namespace Home\Controller;

class ReadController extends HomeController
{
    /**
     * 显示阅读内容
     * @param $cata_id int 该章节的catalog表cata_id值
     */
    public function index($cata_id = null)
    {
        if (empty($cata_id)) {
            $this->error('该章节不存在');
        }

        // 获取章节信息
        $cataData = M('catalog')->where(['cata_status' => 1, 'cata_id' => $cata_id])->field('cata_bid,cata_name')->find();
        if (empty($cataData)) {
            $this->error('该章节不存在');
        }

        $data = $cataData;

        // 获取书籍信息
        $bookData = M('book')->where(['b_status' => 1, 'b_id' => $cataData['cata_bid']])->field('b_name,b_author,b_tid,b_id')->find();
        if (empty($bookData)) {
            $this->error('该书已下架');
        }

        // 更新书籍点击次数
        M('book')->where(['b_id' => $bookData['b_id']])->setInc('b_click');

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
        if (empty($content)) {
            $this->error('该章节不存在');
        }

        // 统计字数
        $encode = 'UTF-8';
        $num = mb_strlen(strip_tags($content['con_content']), $encode);

        $content['num'] = $num;

        // 获取目录
        $catalog = M('catalog')->where(['cata_bid' => $data['cata_bid'], 'cata_status' => 1])->field('cata_id,cata_name')->order('cata_order')->select();

        // 拼接所有目录
        $str = '';
        foreach ($catalog as $v) {
            if ($v['cata_id'] == $cata_id) {
                $str .= '<li style="margin-top: 3px"><a href="' . U('Home/Read/index', ['cata_id' => $v['cata_id']]) . '" class="btn btn-info">' . $v['cata_name'] . '</a></li>';
            } else {
                $str .= '<li style="margin-top: 3px"><a href="' . U('Home/Read/index', ['cata_id' => $v['cata_id']]) . '" class="btn btn-default">' . $v['cata_name'] . '</a></li>';
            }
        }

        $num = 0;
        foreach ($catalog as $k => $v) {
            if ($cata_id == $v['cata_id']) {
                $num = $k;
            }
        }

        // 获取上一个章节
        if ($num == 0) {
            $last = '<a href="#" class="btn btn-default" disabled>已是第一章</a>';
        } else {
            $last = '<a href="' . U('Home/Read/index', ['cata_id' => $catalog[$num - 1]['cata_id']]) . '" class="btn btn-default">上一章</a>';
        }

        // 获取下一个章节
        if ($num == (count($catalog) - 1)) {
            $next = '<a href="#" class="btn btn-default" disabled>已是最后一章</a>';
        } else {
            $next = '<a href="' . U('Home/Read/index', ['cata_id' => $catalog[$num + 1]['cata_id']]) . '" class="btn btn-default">下一章</a>';
        }

        //增加浏览历史
        //判断是否有登录
        if (!empty(session('home_id'))) {
            $bookid = $bookData['b_id'];
            //判断是否已经添加过,添加过的话，更新最后浏览时间
            $map = [];
            $map['h_bid'] = $bookid;
            $map['h_uid'] = session('home_id');
            $history = M('history')->where($map)->select();
            if (empty($history)) {
                $wheres = [];
                $wheres['cata_bid'] = $bookid;
                $firstcata = M('catalog')->where($wheres)->order('cata_order')->select();
                $map['h_time'] = time();
                $map['h_cata'] = $firstcata[0]['cata_id'];
                M('history')->add($map);
            } else {
                $maps['h_time'] = time();
                $maps['h_cata'] = $cata_id;
                $hid = $history[0]['h_id'];
                // var_dump($hid);exit;
                $where = [];
                $where['h_id'] = $hid;
                M('history')->where($where)->save($maps);
            }
        }

        //历史上的今天
        $his = $this->his();

        $this->assign('his', $his);
        $this->assign('last', $last);
        $this->assign('next', $next);
        $this->assign('catalog', $str);
        $this->assign('content', $content);
        $this->assign('data', $data);
        $this->display();
    }

    //新华字典接口
    public function dictionary()
    {
        $info = I('post.val');
        //初始化
        $curl = curl_init();
        $apikey = "564fd402fa4f6e6606ca32a4e372c452";
        $word = urlencode($info);
        //URL 设置
        curl_setopt($curl, CURLOPT_URL, 'http://v.juhe.cn/xhzd/query?key=' . $apikey . '&word=' . $word);
        //将 curl_exec() 获取的信息以 文件流的形式返回,而不是直接输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl执行
        $data = curl_exec($curl);
        //关闭curl
        curl_close($curl);

        //处理JSON数据
        $jsonObj = json_decode($data);
        //提取文章列表
        $xiangjie = $jsonObj->result->jijie;
        if (!empty($xiangjie)) {
            $this->ajaxReturn($xiangjie);
        } else {
            $this->ajaxReturn('err');
        }
    }

    //历史上的今天
    protected function his()
    {
        //初始化
        $curl = curl_init();
        $apikey = "c0ec99b4b714350ba073720e122fab37";
        $m = date('m', time());
        $m = ltrim($m, '0');
        $d = date('d', time());
        $date = $m . '/' . $d;
        //URL 设置
        curl_setopt($curl, CURLOPT_URL, 'http://v.juhe.cn/todayOnhistory/queryEvent.php?key=' . $apikey . '&date=' . $date);
        //将 curl_exec() 获取的信息以 文件流的形式返回,而不是直接输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl执行
        $data = curl_exec($curl);
        //关闭curl
        curl_close($curl);

        //处理JSON数据
        $jsonObj = json_decode($data);
        //提取文章列表
        $today = $jsonObj->result;
        return $today;
    }

}