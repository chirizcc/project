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

    protected function shortMessage($tel)
    {
		//生成一个4位的随机数，作为验证码
		$Random = mt_rand(1000,9999);	
		// var_dump($tel);die;
		$this->sendTemplateSMS($tel,array($Random,'1'),'1');//手机号码，替换内容数组，模板ID

		//返回随机数的值
		return $Random;
    }

	/**
	  * 发送模板短信
	  * @param to 手机号码集合,用英文逗号分开
	  * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
	  * @param $tempId 模板Id,测试应用和未上线应用使用测试模板请填写1，正式应用上线后填写已申请审核通过的模板ID
	*/       
	function sendTemplateSMS($to,$datas,$tempId)
	{
		// var_dump($to);die;

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
	     // 初始化REST SDK
	     // global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
	     $rest = new \Home\Verdor\Sms\REST($serverIP,$serverPort,$softVersion);
	     $rest->setAccount($accountSid,$accountToken);
	     $rest->setAppId($appId);
	    
	     // 发送模板短信
	     //echo "Sending TemplateSMS to $to <br/>";
	     $result = $rest->sendTemplateSMS($to,$datas,$tempId);
	     if($result == NULL ) {
	     	// $this->error('发送失败');
	     }
	     if($result->statusCode!=0) {
	         //echo "error code :" . $result->statusCode . "<br>";
	         //echo "error msg :" . $result->statusMsg . "<br>";
	         //TODO 添加错误处理逻辑

	     	$this->error($result->statusMsg);
	     }else{
	         //echo "Sendind TemplateSMS success!<br/>";
	         // 获取返回信息
	         $smsmessage = $result->TemplateSMS;
	     }
	}

}