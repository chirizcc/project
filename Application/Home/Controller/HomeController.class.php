<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/7
 * Time: 21:37
 */

namespace Home\Controller;

use Think\Controller;

class HomeController extends Controller
{

    public function _empty()
    {
        header('HTTP/1.0 404 not fount');
        $this->display('Public/notFount');
    }

    protected function shortMessage($tel,$timer,$model = 1)
    {
    	include_once("__PUBLIC__/Sms/CCPRestSmsSDK.php");

		//主帐号,对应开官网发者主账号下的 ACCOUNT SID
		$accountSid= '8aaf0708582eefe9015847f6b3231231';

		//主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
		$accountToken= '25da071ee43b4e3684baed8194ec0978';

		//应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
		//在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
		$appId='8aaf0708582eefe9015847f6b3651235';

		//请求地址
		//沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
		//生产环境（用户应用上线使用）：app.cloopen.com
		$serverIP='sandboxapp.cloopen.com';


		//请求端口，生产环境和沙盒环境一致
		$serverPort='8883';

		//REST版本号，在官网文档REST介绍中获得。
		$softVersion='2013-12-26';




		//Demo调用
				//**************************************举例说明***********************************************************************
				//*假设您用测试Demo的APP ID，则需使用默认模板ID 1，发送手机号是13800000000，传入参数为6532和5，则调用方式为           *
				//*result = sendTemplateSMS("13800000000" ,array('6532','5'),"1");																		  *
				//*则13800000000手机号收到的短信内容是：【云通讯】您使用的是云通讯短信模板，您的验证码是6532，请于5分钟内正确输入     *
				//*********************************************************************************************************************
		//生成一个4位的随机数，作为验证码
        $Random  = mt_rand(1000,9999);
		$this->sendTemplateSMS($tel,array($Random,$timer),$model);//手机号码，替换内容数组，模板ID

		//返回随机数的值
		return $Random;
    }

    /**
     * 发送模板短信
     * @param to 手机号码集合,用英文逗号分开
     * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
     * @param $tempId 模板Id,测试应用和未上线应用使用测试模板请填写1，正式应用上线后填写已申请审核通过的模板ID
     */
    private function sendTemplateSMS($to,$datas,$tempId)
    {
        // 初始化REST SDK
        global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
        $rest = new REST($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);

        // 发送模板短信
        //echo "Sending TemplateSMS to $to <br/>";
        $result = $rest->sendTemplateSMS($to,$datas,$tempId);
        if($result == NULL ) {
            $this->error('发送失败');
        }
        if($result->statusCode!=0) {
            //echo "error code :" . $result->statusCode . "<br>";
            //echo "error msg :" . $result->statusMsg . "<br>";
            //TODO 添加错误处理逻辑
            $this->error('发送失败');
        }else{
            //echo "Sendind TemplateSMS success!<br/>";
            // 获取返回信息
            $smsmessage = $result->TemplateSMS;
            $this->success('短信已发送，请在1分钟内填写');
            //echo "dateCreated:".$smsmessage->dateCreated."<br/>";
            //echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
            //TODO 添加成功处理逻辑
        }
    }

    // 发送邮件方法
    protected function sendEmail($to, $title, $content)
    {
        Vendor('PHPMailer.PHPMailerAutoload');
        $mail = new \PHPMailer(); //实例化

        //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        $mail->SMTPDebug = 1;

        //使用smtp鉴权方式发送邮件，当然你可以选择pop方式 sendmail方式等 本文不做详解
        //可以参考http://phpmailer.github.io/PHPMailer/当中的详细介绍
        $mail->isSMTP();
        //smtp需要鉴权 这个必须是true
        $mail->SMTPAuth=true;
        //链接qq域名邮箱的服务器地址
        $mail->Host = C('MAIL_HOST');
        //设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';
        //设置ssl连接smtp服务器的远程服务器端口号 可选465或587
        $mail->Port = C('MAIL_PORT');
        //设置smtp的helo消息头 这个可有可无 内容任意
        //$mail->Helo = 'Hello smtp.qq.com Server';
        //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
        $mail->CharSet = C('MAIL_CHARSET');
        //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = C('MAIL_FROMNAME');
        //smtp登录的账号 这里填入字符串格式的qq号即可
        $mail->Username = C('MAIL_USERNAME');
        //smtp登录的密码 这里填入“独立密码” 若为设置“独立密码”则填入登录qq的密码 建议设置“独立密码”
        $mail->Password = C('MAIL_PASSWORD');

        //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        $mail->From = C('MAIL_FORM');
        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $mail->isHTML(true);
        //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
        $mail->addAddress($to,'尊敬的用户');
        //添加该邮件的主题
        $mail->Subject = $title;
        //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
        $mail->Body = $content;

        if($mail->Send()) {
            //$this->success('发送成功！');
            return true;
        } else {
            //echo $mail->ErrorInfo;
            return false;
        }
    }
}