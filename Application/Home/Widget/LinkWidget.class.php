<?php

namespace Home\Widget;

use Think\Controller;

// 友情链接widget类
class LinkWidget extends Controller
{
    public function getLink()
    {
        $data = M('link')->where('l_state = 0')->select();
        $count = M('link')->where('l_state = 0')->count();
        $num = ceil($count / 3);

        if($num > 4) {
            $num = 4;
        }

        $j = 0;
        for($i = 0; $i < $num; $i++) {
            echo '<div class="col-xs-3">';
            echo '<h4>友情链接</h4>';
            echo '<ul class="list-unstyled">';
            for($m = 0; $m < 3; $m++) {
                echo '<li><a href="'.$data[$j]['l_url'].'">'.$data[$j]['l_name'].'</a></li>';
                $j++;
            }
            echo '</div>';
        }
    }
}