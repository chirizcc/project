<?php
/**
 * 第三方登录控制器
 * author 张德昌
 */
namespace Home\Controller;

use LT\ThinkSDK\ThinkOauth;

class ThreeController extends HomeController
{
    //登录地址
    public function login($type = null)
    {
        empty($type) && $this->error('参数错误');

        //加载ThinkOauth类并实例化一个对象
        $sns = ThinkOauth::getInstance($type);

        //跳转到授权页面
        redirect($sns->getRequestCodeURL());
    }

    //授权回调地址
    public function callback($type = null, $code = null)
    {
        (empty($type) || empty($code)) && $this->error('参数错误');
        //加载ThinkOauth类并实例化一个对象
        $sns = ThinkOauth::getInstance($type);
        //腾讯微博需传递的额外参数
        $extend = null;
        if ($type == 'tencent') {
            $extend = array('openid' => $this->_get('openid'), 'openkey' => $this->_get('openkey'));
        }
        //请妥善保管这里获取到的Token信息，方便以后API调用
        //调用方法，实例化SDK对象的时候直接作为构造函数的第二个参数传入
        //如： $qq = ThinkOauth::getInstance('qq', $token);
        $token = $sns->getAccessToken($code, $extend);
        $sToken = serialize($token);

        $three = M('three');
        $res = $three->where(['th_token' => $sToken])->field('th_uid')->find();
        if (empty($res)) {
            session('sToken', $sToken);
            $this->redirect('Home/Three/newUser', ['type' => $type]);
            die;
        }

        $u_id = $res['th_uid'];
        $user = M('user')->find($u_id);

        if (!empty($user)) {
            if ($user['u_istype'] == '0') {
                $this->error('您的账号被禁用 请联系管理员');
            }
            session('home_name', $user['u_username']);
            session('home_id', $user['u_id']);
            session('home_type', $user['u_istype']);
            $this->success('恭喜您,登录成功!', U('Index/index'));
        } else {
            $this->error('账号或密码错误....');
        }
    }

    /**
     * 第三方账号第一次登录 添加用户表单
     * @param $type string 第三方登录的类型
     */
    public function newUser($type)
    {
        $sToken = session('sToken');
        $token = unserialize($sToken);
        if (empty($token) && !is_array($token)) {
            $this->error('出错了！', U('Home/Reg/index'));
            die;
        }
        $user_info = [];
        if (is_array($token)) {
            $user_info = A('Type', 'Event')->$type($token);
        }
        $this->assign('data', $user_info);
        $this->assign('type', $type);
        $this->display();
    }

    /**
     * 第三方账号第一次登录 从POST获取数据 并存入数据库
     */
    public function insertUser()
    {
        $user = D('user');
        if ($user->create()) {
            $u_id = $user->add();
            if ($u_id) {
                $maps = ['det_uid' => $u_id, 'det_name' => I('post.u_username')];
                M('detail')->add($maps);

                $sToken = session('sToken');
                $data = ['th_uid' => $u_id, 'th_token' => $sToken];
                M('three')->create($data);
                M('three')->add();

                session('home_name', I('post.u_username'));
                session('home_id', $u_id);
                session('home_type', 3);
                $this->redirect('Home/Index/index');
                die;
            } else {
                $this->error('出错了，请稍后再试!', U('Home/Reg/index'));
            }
        } else {
            $this->error('出错了，请稍后再试!', U('Home/Reg/index'));
        }
    }
}