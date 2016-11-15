<?php

namespace Admin\Model;

use Think\Model;

class LinkModel extends Model
{
    protected $_validate = array(
        array('l_name','','该链接已经存在！请不要重复添加',0,'unique',1), // 在新增的时候验证name字段是否唯一
        array('l_name','require','链接名不能为空',0,'regex',self::MODEL_BOTH),
        array('l_url','url','URL格式不正确',0,'regex',self::MODEL_BOTH),
    );
}