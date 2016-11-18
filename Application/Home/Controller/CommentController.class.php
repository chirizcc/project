<?php

namespace Home\Controller;

class CommentController extends JudgeController
{
    public function insert()
    {
        $u_id = session('home_id');
        $data = $_POST;
        $data['com_uid'] = $u_id;

        $com = D('comment');
        if($com->create($data)) {
            if($com->add()) {
                $this->success('评论成功!');
            } else {
                $this->error('评论失败，请稍后再试!');
            }
        } else {
            $this->error($com->getError());
        }

    }
}