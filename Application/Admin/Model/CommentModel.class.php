<?php

namespace Admin\Model;

use Think\Model;

class CommentModel extends Model
{
    protected $_auto = array (
        array('com_time','time',1,'function'),//生成评论时自动添加时间
    );
}