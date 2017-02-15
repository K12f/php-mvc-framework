<?php

namespace core;

use core\Config;    //使用配置类
use core\Parser;    //使用模板解析类

class View
{
	public $vars = [];
	
	public function __construct($var = [])
	{
		if (!is_dir(Config::get('cache_path')) || !is_dir(Config::get('compile_path')) || !is_dir(Config::get('view_path'))) {
			exit('The directory does not exist');
		}
		$this->vars = $var;
	}
	
	//展示模板
	public function display($file)
	{
		$tpl_file = Config::get('view_path') . $file . Config::get('view_suffix');
		
		if (!file_exists($tpl_file)) {
			exit('temple file does not exist');
		}
		//编译文件(文件名 用md5 加密加上原始文件名
		$parser_file = Config::get('compile_path') . md5($file)  . '.php';
		//缓存文件 （缓存前缀加原始文件名
		$cache_file = Config::get('cache_path') . Config::get('cache_prefix') . $file . '.html';
		
		//是否开启自动缓存
		if (Config::get('auto_cache')) {
			
			if (file_exists($cache_file) && file_exists($parser_file)) {
				if (filemtime($cache_file) >= filemtime($parser_file) && filemtime($parser_file) > filemtime($tpl_file)) {
					return include $cache_file;
				}
			}
			
		}
			//是否需要重修编译模板
			if (!file_exists($parser_file) || filemtime($parser_file) < filemtime($tpl_file)) {
				$parser = new Parser($tpl_file);
				$parser->compile($parser_file);
			}
		if (Config::get('auto_cache')) {
			
			file_put_contents($cache_file, ob_get_contents());
			ob_end_clean();
		}
		include $parser_file;
	}
}
