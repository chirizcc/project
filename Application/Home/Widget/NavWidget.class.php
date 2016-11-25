<?php
/**
 * 导航栏Widget类
 */
namespace Home\Widget;

use Think\Controller;

class NavWidget extends Controller
{
    /**
     * 输出导航栏
     */
    public function getNav()
    {
        $headerType = M('type')->where(['t_pid' => 0])->limit(6)->select();
        $count = M('type')->where(['t_pid' => 0])->count();
        $otherType = M('type')->where(['t_pid' => 0])->limit(6, $count - 6)->select();

        echo '<ul class="nav navbar-nav">';

        foreach ($headerType as $v) {
            echo '<li><a href="' . U('Home/Type/index', ['t_pid' => $v['t_id']]) . '">' . $v['t_name'] . '</a></li>';
        }
        echo '<li class="dropdown">';
        echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown">更多 <span class="caret"></span></a>';
        echo '<ul class="dropdown-menu" role="menu">';
        for ($i = 0; $i < count($otherType); $i++) {
            echo '<li><a href="' . U('Home/Type/index', ['t_pid' => $otherType[$i]['t_id']]) . '">' . $otherType[$i]['t_name'] . '</a></li>';
            if ($i != count($otherType) - 1) {
                echo '<li class="divider"></li>';
            }
        }
        echo '</ul>';
        echo '</li>';

        echo '</ul>';
    }
}