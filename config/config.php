<?php

return [
	'db_host' => '127.0.0.1',
	'db_user' => 'root',
	'db_pwd' => '',
	'db_name' => 'labframe',
	'db_table_prefix' => 'lab_',
	'db_charset' => 'utf8',
	
	'default_module' => 'home',
	'default_controller' => 'Index',
	'default_action' => 'index',
	'url_type' => 2, // RUL模式：【1：普通模式，采用传统的 url 参数模式】【2：PATHINFO 模式，也是默认模式】
	
	'cache_path' => RUNTIME_PATH . 'cache' . DS,  //缓存路径
	'cache_prefix' => 'cache_', //缓存文件前缀
	'cache_type' => 'file', //缓存文件类型
	'compile_path' => RUNTIME_PATH . 'compile' . DS,  //编译文件存放路径

	'view_path' => APP_PATH . 'home' . DS . 'view' . DS,  //模板路径
	'view_suffix' => '.php', //模板后缀
	
	'auto_cache' => false, //自动开启缓存
	'url_html_suffix' => 'html' //url伪静态后缀
];