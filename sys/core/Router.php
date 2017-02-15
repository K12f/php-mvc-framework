<?php

namespace core;

class Router
{
	public $url_query;  //url 串
	public $url_type;   //url模式
	public $route_url = []; //url数组
	
	
	public function __construct()
	{
		$this->url_query = parse_url($_SERVER['REQUEST_URI']);
	}
	
	//设置url模式
	public function setUrlType($url_type = 2)
	{
		if ($url_type > 0 && $url_type < 3) {
			$this->url_type = $url_type;
		} else {
			exit('指定url 模式不存在');
		}
	}
	
	//获取url数组
	public function getUrlArray()
	{
		$this->makeUrl();
		return $this->route_url;
	}
	
	//处理url
	public function makeUrl()
	{
		switch ($this->url_type) {
			case 1:
				$this->queryToArray();
				break;
			case 2:
				$this->pathinfoToArray();
				break;
			default:
				$this->pathinfoToArray();
				break;
		}
	}
	
	//将参数形式转为数组
	public function queryToArray()
	{
		//?xx=xx&xx=xx
		$arr = !empty($this->url_query['query']) ? explode('&', $this->url_query['query']) : [];
		$array = $tmp = [];
		if (count($arr) > 0) {
			foreach ($arr as $item) {
				$tmp = explode('=', $item);
				$array[$tmp[0]] = $tmp[1];
			}
			if (isset($array['module'])) {
				$this->route_url['module'] = $array['module'];
				unset($array['module']);
			}
			if (isset($array['controller'])) {
				$this->route_url['controller'] = $array['controller'];
				unset($array['controller']);
			}
			if (isset($array['action'])) {
				$this->route_url['action'] = $array['action'];
				unset($array['action']);
			}
			if (isset($this->route_url['action']) && strpos($this->route_url['action'], '.')) {
				//判断url方法名后缀 如 index.html ，气体必须要在地址中以 localhost:8080/index.php开始
				if (explode('.', $this->route_url['action'][1] !== Config::get('url_html_suffix'))) {
					exit('suffix error');
				} else {
					$this->route_url['action'] = explode('.', $this->route_url['action'])[0];
				}
			}
		} else {
			$this->route_url = [];
		}
	}
	
	//将pathinfo转为数组
	public function pathinfoToArray()
	{
		$url = '';
		if (0 === strpos($this->url_query['path'], '/')) {
			$url = mb_substr($this->url_query['path'], 1, null, 'utf8');
		}
		if (mb_strlen($url, 'utf8') > 0) {
			$arr = explode('/', $url);
			if ($arr[0] === 'index.php') {
				array_shift($arr);
				if (isset($arr) && !empty($arr)) {
					$this->route_url['module'] = array_shift($arr);
				}
				if (isset($arr) && !empty($arr)) {
					$this->route_url['controller'] = array_shift($arr);
				}
				if (isset($arr) && !empty($arr)) {
					$this->route_url['action'] = array_shift($arr);
				}
				//判断url后缀名
				if (isset($this->route_url['action']) && strpos($this->route_url['action'], '.')) {
					if (explode('.', $this->route_url['action'])[1] !== Config::get('url_html_suffix')) {
						exit('incorrect url suffix');
					} else {
						$this->route_url['action'] = explode('.', $this->route_url['action'])[0];
					}
				}
				//参数
				if (count($arr) > 0) {
					foreach ($arr as $k => $v) {
						if (($k & 1)) {
							$this->route_url['params'][] = $arr[$k];
						}
					}
				}
			} else {
				if (isset($arr[1]) && !empty($arr[1])) {
					$this->route_url['module'] = $arr[1];
				}
				if (isset($arr[2]) && !empty($arr[2])) {
					$this->route_url['controller'] = $arr[2];
				}
				if (isset($arr[3]) && !empty($arr[3])) {
					$this->route_url['action'] = $arr[3];
				}
			}
		}
	}
}