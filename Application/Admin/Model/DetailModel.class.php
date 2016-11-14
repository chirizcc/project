<?php

namespace Admin\Model;

use Think\Model;

class DetailModel extends Model
{
    protected $_validate = array(
        array('det_tel','/^1[3|4|5|7|8][0-9]\d{8}$/','手机号码格式不正确！'), // 正则验证用户名
        array('det_email','email','邮箱格式不正确'),
    );
}