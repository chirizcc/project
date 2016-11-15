<?php

namespace Home\Widget;

use Think\Controller;

// 导航栏widget
class NavWidget extends Controller
{
    public function getNav()
    {
        $headerType = M('type')->where(['t_pid' => 0])->limit(6)->select();
        $count = M('type')->where(['t_pid' => 0])->count();
        $otherType = M('type')->where(['t_pid' => 0])->limit(6,$count - 6)->select();

        echo '<ul class="nav navbar-nav">';

        foreach ($headerType as $v) {
            echo '<li><a href="#">'.$v['t_name'].'</a></li>';
        }
        echo '<li class="dropdown">';
        echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">更多 <span class="caret"></span></a>';
        echo '<ul class="dropdown-menu" role="menu">';
        for ($i = 0; $i < count($otherType); $i++) {
            echo '<li><a href="#">'.$otherType[$i]['t_name'].'</a></li>';
            if($i != count($otherType) - 1) {
                echo '<li class="divider"></li>';
            }
        }
        echo '</ul>';
        echo '</li>';

        echo '</ul>';
    }
}