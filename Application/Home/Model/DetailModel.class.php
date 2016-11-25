<?php
/**
 * 用户像详情表Model类
 * author 张德昌
 */
namespace Home\Model;

use Think\Model;

class DetailModel extends Model
{
    protected $_validate = array(
        array('det_tel', '/^1[3|4|5|7|8][0-9]\d{8}$/', '手机号码格式不正确！'), // 正则验证手机号
        array('det_email', 'email', '邮箱格式不正确'), // 正则验证邮箱
    );
}