<?php

namespace core; //定义命名空间

use core\Config;    //使用配置类

use core\Router;     //使用路由类


class APP
{
	public static $router;  //定义一个静态路由实例
	
	//启动
	public static function run()
	{
		self::$router = new Router();
		self::$router->setUrlType(Config::get("url_type")); //读取配置并设置路由类型
		$url_array = self::$router->getUrlArray();  //获取经过路由类处理生成的路由数组
		
		self::dispatch($url_array); //根据路由数组分发路由
	}
	
	//路由分发
	public static function dispatch($url_array = [])
	{
		$module = '';
		$controller = '';
		$action = '';
		$params = '';
		if (isset($url_array['module'])) {
			$module = $url_array['module'];
		} else {
			$module = Config::get('default_module');
		}
		if (isset($url_array['controller'])) {
			$controller = ucfirst($url_array['controller']);
		} else {
			$controller = ucfirst(Config::get('default_controller'));
		}
		
		$controller_file = APP_PATH . $module . DS . 'controller' . DS . $controller . 'Controller.php';
		if (isset($url_array['action'])) {
			$action = $url_array['action'];
		} else {
			$action = Config::get('default_action');
		}
		
		if (isset($url_array['params']) && !empty($url_array['params'])) {
			$params = $url_array['params'];
		}
		
		//判断控制器文件是否存在
		if (file_exists($controller_file)) {
//			require $controller_file;
			$className = 'module\controller\IndexController';       //命名空间字符串示例
			$className = str_replace('module', $module, $className);   //使用字符串替换功能，替换对应的模块名和控制器名
			$className = str_replace('IndexController', $controller . 'Controller', $className);
			$controller = new $className;
			
			//判断访问的方法是否存在
			if (method_exists($controller, $action)) {
				$controller->setTpl($action);   //设置方法队长的视图模板
				$params?call_user_func_array([$controller,$action],$params):call_user_func([$controller,$action]);
			} else {
				die('the method does not exist');
			}
		} else {
			die('the controller does not exist');
		}
	}
}






