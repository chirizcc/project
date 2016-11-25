<?php
/**
 * 等待激活表Model类
 */
namespace Home\Model;

use Think\Model;

class WaitModel extends Model
{
    protected $_validate = array(
        array('w_email', 'email', '邮箱格式不正确'), // 在新增更新时验证邮箱格式
    );
}