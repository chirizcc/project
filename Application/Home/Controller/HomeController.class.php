<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/7
 * Time: 21:37
 */

namespace Home\Controller;

use Think\Controller;

class HomeController extends Controller
{
    public function _empty()
    {
        header('HTTP/1.0 404 not fount');
        $this->display('Public/notFount');
    }
}