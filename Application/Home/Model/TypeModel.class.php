<?php
/**
 * 分类表Model类
 * author 张德昌
 */
namespace Home\Model;

use Think\Model;

class TypeModel extends Model
{
    /**
     * 按照级别分类获取所有分类
     * @return array 返回已经重组好的数组 子分类保存在父分类的child字段里
     */
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