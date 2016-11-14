<?php

namespace Home\Model;

use Think\Model;

class UserModel extends Model
{
    protected $_validate = array(
        array('u_username','','该用户名已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
        array('u_username','/^[a-zA-z][a-zA-Z0-9_]{2,9}$/','用户名格式不正确！',0,'regex',1), // 正则验证用户名
        array('u_password','/((?=.*\d)(?=.*\D)|(?=.*[a-zA-Z])(?=.*[^a-zA-Z]))^.{8,16}$/','密码格式不正确',0,'regex',1), // 验证密码格式 长度8~16位 数字、字母、字符至少包含两种。
    );

    protected $_auto = array (
        array('u_password','md5',3,'function') , // 对password字段在新增和编辑的时候使md5函数处理
        array('u_regtime','time',1,'function'), // 对update_time字段在更新的时候写入当前时间戳
    );
}