<?php
namespace Home\Controller;

use Think\Controller;

class ContentController extends JudgeController
{
    public function index($cata_id = null)
    {
        if (empty($cata_id)) {
            $this->redirect('Home/Authoredit/index');
            die;
        }

        $content = $this->isExist($cata_id);
        // 查询数据
        $cata_name = M('catalog')->field('cata_name')->find($cata_id);
        // 发送数据
        $this->assign('cata_id', $cata_id);
        $this->assign('cata_name', $cata_name['cata_name']);
        $this->assign('data', $content);
        $this->display();
    }
    // 更新页面
    public function edit($cata_id = null)
    {
        if (empty($cata_id)) {
            $this->redirect('Home/Authoredit/index');
            die;
        }

        $content = $this->isExist($cata_id);
        // 查询符合条件的数据
        $cata_name = M('catalog')->field('cata_name')->find($cata_id);
        // 发送数据
        $this->assign('cata_id', $cata_id);
        $this->assign('cata_name', $cata_name['cata_name']);
        $this->assign('data', $content);
        $this->display();
    }
    // 更新
    public function update($cata_id = null)
    {
        $con = D('content');
        if ($con->create($_POST)) {
            if ($con->save()) {
                // 编辑过内容，这将该目录状态更改为未审核
                $data['cata_status'] = 0;
                $data['cata_id'] = $cata_id;
                M('catalog')->save($data);
                $this->success('修改成功', U('Home/Content/index', ['cata_id' => $cata_id]));
            } else {
                $this->error('修改失败');
            }
        } else {
            $this->error($con->getError());
        }
    }

    /**
     * 判断该章节的内容表是否存在
     * @param $cata_id
     * @return mixed
     */
    public function isExist($cata_id)
    {
        $content = M('content')->where(['con_catid' => $cata_id])->find();
        if ($content == null) {
            $cata = M('catalog')->find($cata_id);
            if ($cata == null) {
                $this->redirect('Home/Authoredit/index');
                die;
            }
            $newContent = ['con_catid' => $cata_id];
            if (!D('content')->create($newContent)) {
                $this->redirect('Home/Authoredit/index');
                die;
            }
            if (!D('content')->add()) {
                $this->redirect('Home/Authoredit/index');
                die;
            }
            $content = M('content')->where(['con_catid' => $cata_id])->find();
        }
        return $content;
    }
}