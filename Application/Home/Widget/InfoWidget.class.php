<?php

namespace Home\Widget;

use Think\Controller;

//检查是否有新消息
class InfoWidget extends Controller
{
	public function getNewmessage()
	{
		$info = M('info');
		$map = [];
		$map['i_status'] = '0';
		$map['i_uid'] = session('home_id');
		$num = $info->where($map)->count();

		if($num !== 0){
			echo $num;
		}
	}

}