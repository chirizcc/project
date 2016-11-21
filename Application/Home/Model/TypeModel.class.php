<?php

namespace Home\Model;

use Think\Model;

class TypeModel extends Model
{
    public function getTypes()
    {
        $data = $this->where(['t_pid' => 0])->field('t_id, t_name')->select();

        foreach ($data as $k => $v) {
            $child = $this->where(['t_pid' => $v['t_id']])->field('t_id, t_name')->select();
            $data[$k]['child'] = $child;
        }
        
        return $data;
    }
}