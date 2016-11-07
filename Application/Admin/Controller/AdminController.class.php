<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/7
 * Time: 21:37
 */

namespace Admin\Controller;

use Think\Controller;

class AdminController extends Controller
{
    public function _empty()
    {
        $this->display('Public/notFount');
    }
}