<?php

namespace Home\Model;

use Think\Model;

class WaitModel extends Model
{
    protected $_validate = array(
        array('w_email', 'email', '邮箱格式不正确'),
    );
}