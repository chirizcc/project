<?php

namespace Home\Model;

use Think\Model;

class CommentModel extends Model
{
    protected $_validate = array(
        array('com_content','require','请填写评论内容！'), //默认情况下用正则进行验证
        array('com_bid','require','评论失败！'), //默认情况下用正则进行验证
    );

    protected $_auto = array (
        array('com_time','time',1,'function'), // 对update_time字段在更新的时候写入当前时间戳
    );
}