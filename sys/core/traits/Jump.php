<?php

namespace core\traits;

trait Jump
{
	public function success($msg = '', $url = '', $data = '')
	{
		$code = 1;
		if (is_numeric($msg)) {
			$code = $msg;
			$msg = '';
		}
		if (is_null($url) && isset($_SERVER['HTTP_REFERER'])) {
			$url = $_SERVER['HTTP_REFERER'];
		}
		$result = [
			'code' => $code,  //状态码
			'msg' => $msg,    //'显示信息'
			'data' => $data,  //'输出数据'
			'url' => $url,    //url
		];
		
		$output = "code: " . $result['code'] . PHP_EOL . 'msg:' . $result['msg'] . PHP_EOL . 'data:' . $result['data'];
		echo "<script>alert('$output');location.href = {$result['url']}</script>";
		exit();
	}
	
	public function error($msg = '', $url = '', $data = '')
	{
		$code = 0;
		if (is_numeric($msg)) {
			$code = $msg;
			$msg = '';
		}
		if (is_null($url) && isset($_SERVER['HTTP_REFERER'])) {
			$url = $_SERVER['HTTP_REFERER'];
		}
		$result = [
			'code' => $code,  //状态码
			'msg' => $msg,    //'显示信息'
			'data' => $data,  //'输出数据'
			'url' => $url,    //url
		];
		
		$output = "code: " . $result['code'] . PHP_EOL . 'msg:' . $result['msg'] . PHP_EOL . 'data:' . $result['data'];
		echo "<script>alert('$output');location.href = {$result['url']}</script>";
		exit();
	}
}