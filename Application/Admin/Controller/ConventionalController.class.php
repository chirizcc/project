<?php 
namespace Admin\Controller;
use Think\Controller;

class ConventionalController extends Controller
{
	public function index() 
	{
		// 获取系统数据
		$server=$_SERVER;
		// 发送系统数据
		$this->assign('server',$server);		
		$this->display('Conventional/index');
	}

	public function weather() 
	{
		$cityName = empty(I('get.city'))?'上海':I('get.city');
		$city = iconv('UTF-8', 'GBK', $cityName);
		$url = 'http://php.weather.sina.com.cn/xml.php?city='.$city.'&password=DJOYnieT8234jlsK&day=0';

		//curl初始化
		$curl = curl_init();
		// var_dump($curl);//得到资源

		//URL 设置
		curl_setopt($curl, CURLOPT_URL, $url);
		//将 curl_exec() 获取的信息以 文件流的形式返回,而不是直接输出
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		//curl执行
		$data = curl_exec($curl);
		// var_dump($data);//得到的是一个xml字串
		/*echo '<pre>';
		    // print_r($data);//不显示标签
		    echo htmlspecialchars($data);
		echo '</pre>';*/

		//关闭curl
		curl_close($curl);

		//处理XML数据
		$obj = simplexml_load_string($data);
		// var_dump($obj);//得到SimpleXMLElement对象
		if(empty($obj->Weather)){
			$this->ajaxReturn(0);
		}else{

        $this->ajaxReturn($obj);
		}
	}




}
