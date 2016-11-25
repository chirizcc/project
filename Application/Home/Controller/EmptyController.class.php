<?php
/**
 * 空控制器
 * author 张德昌
*/
namespace Home\Controller;

use Think\Controller;

class EmptyController extends Controller
{
    public function _empty()
    {
        header('HTTP/1.0 404 not fount');
        $this->display('Public/notFount');
    }
}