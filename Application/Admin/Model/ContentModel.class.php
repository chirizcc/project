<?php

namespace Admin\Model;

use Think\Model;

class ContentModel extends Model
{
    protected $_validate = array(
        array('con_catid','require','章节不能为空！',0,'regex',3), // 验证书籍名不能为空
    );

    protected $_auto = array (
        array('con_time','time',3,'function'), // 对con_time字段在任何时候都进行更新
        array('con_content','',1,'string'), // 对con_content字段在添加新章节是为空
    );
}