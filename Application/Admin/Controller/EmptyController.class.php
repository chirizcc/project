<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/7
 * Time: 22:01
 */

namespace Admin\Controller;

use Think\Controller;

class EmptyController extends Controller
{
    public function _empty()
    {
        header('HTTP/1.0 404 not fount');
        $this->display('Public/notFount');
    }
}