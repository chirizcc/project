<?php

namespace Admin\Model;

use Think\Model;

// book表模型类
class BookModel extends Model
{
    protected $_validate = array(
        array('b_name','require','书籍名不能为空！',0,'regex',3), // 验证书籍名不能为空
        array('b_tid','require','请选择分类！',0,'regex',3), // 验证分类不能为空
        array('b_tid',[0],'请选择分类！',0,'notin',3), // 验证分类不能为空
    );

    protected $_auto = array (
        array('b_time','time',3,'function'), // 对update_time字段在更新的时候写入当前时间戳
    );
}