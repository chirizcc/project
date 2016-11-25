<?php
/**
 * 目录管理控制器
 * author 张德昌
 */
namespace Admin\Controller;


class CatalogController extends AdminController
{
    // 目录列表每页显示条数
    private $size = 5;

    /**
     * 构造函数 判断session中是否存有书的ID
     */
    public function __construct()
    {
        parent::__construct();
        if (ACTION_NAME != 'index') {
            if (empty(session('b_id'))) {
                $this->redirect('Admin/Book/index');
            }
        }
    }

    /**
     * 显示该书的目录列表
     * @param $b_id int 该书的ID
     * @param $search string 搜索参数
     */
    public function index($b_id = null, $search = null)
    {
        if (empty($b_id)) {
            if (empty(session('b_id'))) {
                $this->redirect('Admin/Book/index');
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
        // 查询数据
        $result = M('book')->where(['b_id' => $b_id])->field('b_name')->find();
        $name = $result['b_name'];

        session('b_id', $b_id);
        session('b_name', $name);

        $data = M('catalog')->where(['cata_bid' => $b_id])->where($map)->order('cata_order')->page($_GET['p'], $this->size)->select();
        $count = M('catalog')->where(['cata_bid' => $b_id])->where($map)->count();// 查询满足要求的总记录数
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

    /**
     * 添加目录页面
     */
    public function add()
    {
        $this->display();
    }

    /**
     * 从POST中获取添加的目录数据并写入数据库
     */
    public function insert()
    {
        $catalog = M('catalog');
        $data = I('post.');

        if (empty(I('post.cata_name'))) {
            $this->error('章节名不能为空！');
            die;
        }

        $res = $catalog->where(['cata_bid' => session('b_id')])->max('cata_order');
        $order = $res + 1;
        $data['cata_order'] = $order;

        if ($catalog->create($data)) {
            if (($cata_id = $catalog->add()) > 0) {
                $data = ['con_catid' => $cata_id];
                $content = D('content');
                if ($content->create($data)) {
                    if ($content->add() > 0) {
                        $this->success('添加成功！', U('Admin/Catalog/index', ['b_id' => session('b_id')]));
                    }
                } else {
                    $catalog->delete($cata_id);
                }
            }
        }

        $this->error('添加失败，请稍后再试！');
    }

    /**
     * 响应AJAX的请求，改变目录的排序
     */
    public function changeOrder()
    {
        $catalog = M('catalog');

        if ($catalog->where(['cata_id' => I('post.cata_id')])->setField('cata_order', I('post.cata_order'))) {
            $this->ajaxReturn(true);
        }

        $this->ajaxReturn(false);
    }

    /**
     * 编辑目录的页面
     * @param $cata_id int 该目录的catalog表的cata_id值
     */
    public function edit($cata_id = null)
    {
        if (empty($cata_id)) {
            $this->redirect('Admin/Authoredit/index');
            die;
        }

        $data = M('catalog')->find($cata_id);
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 从POST获取编辑的目录书籍，并更新数据库
     */
    public function update()
    {
        $cata = M('catalog');
        if ($cata->create()) {
            if ($cata->save()) {
                $this->success('修改成功', U('Admin/Catalog/index', ['b_id' => session('b_id')]));
            } else {
                $this->error('修改失败，请稍后再试！');
            }
        } else {
            $this->error($cata->getError());
        }
    }

    /**
     * 响应AJAX，删除指定章节及内容
     * @param $cata_id int 该章节的catalog表的cata_id值
     * @return boolean 是否删除成功
     */
    public function del($cata_id = null)
    {
        if (empty($cata_id)) {
            $this->ajaxReturn(false);
        }

        $cata = M('catalog');
        if ($cata->delete($cata_id)) {
            M('content')->where(['con_catid' => $cata_id])->delete();
            $this->ajaxReturn(true);
        } else {
            $this->ajaxReturn(false);
        }
    }
}