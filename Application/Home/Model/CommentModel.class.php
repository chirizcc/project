<?php
/**
 * 评论表Model类
 * author 张德昌
 */
namespace Home\Model;

use Think\Model;

class CommentModel extends Model
{
    protected $_validate = array(
        array('com_content', 'require', '请填写评论内容！'), //验证com_content评论内容不能为空
        array('com_bid', 'require', '评论失败！'), //验证com_bid评论的书籍id不能为空
    );

    protected $_auto = array(
        array('com_time', 'time', 1, 'function'), // 对com_time字段在更新的时候写入当前时间戳
    );
}