<?php
/**
 * 前台注册控制器
 * author 张德昌 沈悦群
 */
namespace Home\Controller;

class RegController extends HomeController
{
    /**
     * 显示注册页面
     */
    public function index()
    {
        if (!empty(session('home_name'))) {
            $this->redirect('Index/index');
            exit;
        }
        $this->display();
    }

    /**
     * 更新验证码
     */
    public function code()
    {
        $Verify = new \Think\Verify();
        $Verify->fontSize = 30;
        $Verify->length = 3;
        $Verify->imageW = 0;
        $Verify->imageH = 0;
        $Verify->entry();
    }

    /**
     * 通过ajax验证验证码是否正确
     * @return boolean 返回验证码是否正确
     */
    public function codetest()
    {
        if (!IS_AJAX) {
            $this->error('快回去别闹', U('Index/index'));
            exit;
        }
        $val = I('post.val');

        if ((new \Think\Verify())->check($val)) {
            $this->ajaxReturn(true);
        } else {
            $this->ajaxReturn(false);
        }
    }

    /**
     * 通过ajax验证注册时用户名是否已存在
     * @return boolean 返回该用户名是否存在
     */
    public function usernametest()
    {
        if (!IS_AJAX) {
            $this->error('别乱玩！', U('Index/index'));
            exit;
        }

        $val = I('post.val');
        $val = trim($val);

        //判断不能为空
        if ($val == null) {
            $this->ajaxReturn('null');
            exit;
        }

        //正则验证
        if (!preg_match('/^[a-zA-z][a-zA-Z0-9_]{2,9}$/', $val)) {
            $this->ajaxReturn('err');
            exit;
        }

        //验证是否有重复
        $map['u_username'] = $val;
        if (empty(M('user')->where($map)->select())) {
            $this->ajaxReturn(true);
        } else {
            $this->ajaxReturn(false);
        }
    }

    /**
     * 通过ajax验证密码与确认密码是否相等
     * @return boolean
     */
    public function passwordtest()
    {
        if (!IS_AJAX) {
            $this->error('滚吧', U('Index/index'));
            exit;
        }

        $val1 = I('post.val1');
        $val2 = I('post.val2');

        //判断不能为空
        if ($val1 == null) {
            $this->ajaxReturn('null1');
            exit;
        }
        if ($val2 == null) {
            $this->ajaxReturn('null2');
            exit;
        }

        //正则验证
        if (!preg_match('/((?=.*\d)(?=.*\D)|(?=.*[a-zA-Z])(?=.*[^a-zA-Z]))^.{8,16}$/', $val1)) {
            $this->ajaxReturn('err');
            exit;
        }

        if ($val1 == $val2) {
            $this->ajaxReturn('true');
        } else {
            $this->ajaxReturn('false');
        }
    }

    //执行注册
    public function reg()
    {
        if (empty($_POST)) {
            $this->redirect('Home/Reg/index');
            exit;
        }

        D('user')->create();
        //执行添加
        $userid = D('user')->add();
        if ($userid > 0) {

            $maps = [];
            $maps['det_uid'] = $userid;
            $maps['det_tel'] = $_POST['u_tel'];
            $maps['det_name'] = $_POST['u_username'];

            if (M('detail')->add($maps) > 0) {
                $this->success('恭喜您,注册成功!', U('Login/index'));
            } else {
                $this->error('用户详情添加失败....');
            }

        } else {
            $this->error('添加失败....');
        }
    }

    /**
     * 邮箱注册发送邮件
     */
    public function emailReg()
    {
        $data = I('post.');
        $email = I('post.email');

        if (!empty(M('user')->where(['u_username' => I('post.u_username')])->find())) {
            $this->error('该用户名已被注册！');
        }

        $data['u_username'] = I('post.u_username');
        $data['u_istype'] = 0;

        $user = D('user');
        if ($user->create($data)) {
            $uid = $user->add();
            if ($uid) {
                $wait = D('wait');
                $wData = ['w_uid' => $uid, 'w_email' => $email];
                if ($wait->create($wData)) {
                    $wid = $wait->add();
                    if ($wid) {
                        $title = '注册验证';
                        // 发送到邮箱的链接采用base64加密
                        $content = '尊敬的用户' . I('post.u_username') . ': 感谢您注册终点中文网，您可以通过点击以下链接激活您的账号: <a href="' . U('Home/Reg/activa', ['w_id' => base64_encode($wid)], 'html', true) . '">' . U('Home/Reg/activa', ['w_id' => base64_encode($wid)], 'html', true) . '</a>';
                        if ($this->sendEmail($email, $title, $content)) {
                            $this->success('邮件发送成功，请前往激活您的账号！', U('Home/Reg/wait'));
                        } else {
                            $this->error('注册失败，请稍后再试！');
                        }
                    } else {
                        $this->error('注册失败，请稍后再试！');
                    }
                } else {
                    $this->error($wait->getError());
                }
            } else {
                $this->error('注册失败，请稍后再试！');
            }
        } else {
            $this->error($user->getError());
        }
    }

    /**
     * 邮箱激活账号
     * @param $w_id int wait表的w_id值
     */
    public function activa($w_id = null)
    {
        if (empty($w_id)) {
            $this->redirect('Home/Index/index');
        }

        $w_id = base64_decode($w_id);
        $res = D('wait')->where(['w_id' => $w_id])->find();
        if (!$res) {
            $this->error('验证出错，请重新注册！', U('Home/Reg/index'));
        }

        D('user')->where(['u_id' => $res['w_uid']])->save(['u_istype' => 3]);
        D('detail')->add(['det_uid' => $res['w_uid'], 'det_name' => 'ls' . mt_rand(10000, 99999), 'det_email' => $res['w_email']]);
        D('wait')->where(['w_id' => $w_id])->delete();

        $this->success('激活成功!', U('Home/Login/index'));
    }

    /**
     * 显示邮件发送成功页面
     */
    public function wait()
    {
        if (!empty(session('home_name'))) {
            $this->redirect('Index/index');
            exit;
        }
        $this->display();
    }

    //发送短信
    public function sendSms()
    {
        if (!IS_AJAX) {
            $this->error('滚吧', U('Index/index'));
            exit;
        }

        //正则验证 
        $tel = I('post.val');
        if (!preg_match('/^1[3|4|5|7|8][0-9]\d{8}$/', $tel)) {
            $this->ajaxReturn('err');
            exit;
        } else {
            $sms = $this->shortMessage($tel);
            session('sms', $sms);
        }

    }

    //判断验证码是否一致
    public function comparesms()
    {
        if (!IS_AJAX) {
            $this->error('滚吧', U('Index/index'));
            exit;
        }

        $val = I('post.val');
        if ($val == session('sms')) {
            $this->ajaxReturn(true);
        } else {
            $this->ajaxReturn(false);
        }
    }

}