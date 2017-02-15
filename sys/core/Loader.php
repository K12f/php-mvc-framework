<?php

namespace core;

class Loader
{
	
	/*
	 *  array关联数组，其中键是命名空间前缀和值 是该命名空间中的类的基本目录数组
	 */
	protected static $prefixes = [];
	
	public static function register()
	{
		spl_autoload_register('core\\Loader::loadClass');
	}
	
	/**
	 * 添加命名空间前缀与文件base目录对
	 * @param string $prefix 命名空间前缀
	 * @param string $base_dir 命名空间中类文件的基目录
	 * @param bool $prepend 为 true时，将基目录查到最前，这将让他第一个被搜索到，否则最后
	 */
	public static function addNamespace(string $prefix, string $base_dir, bool $prepend = false)
	{
		//规范化命令空间前缀
		$prefix = trim($prefix, '\\') . '\\';
		//规范化文件基目录
		$base_dir = rtrim($base_dir, DS) . '/';
		
		//初始化命令空间前缀数组
		if (isset(self::$prefixes[$prefix]) === false) {
			self::$prefixes[$prefix] = [];
		}
		
		//将命名空间前缀与文件基目录对插入保存数组
		if ($prepend) {
			array_unshift(self::$prefixes[$prefix], $base_dir);
		} else {
			array_push(self::$prefixes[$prefix], $base_dir);
		}
		
	}
	
	
	public static function loadClass($class)
	{
		dump($class);
		//当前命名空间 前缀
		$prefix = $class;
		
		//从后面开始遍历完全合格类名中的命名空间名称，来查找映射的文件名
		while (false !== $pos = strrpos($prefix, '\\')) {
			//保留命名空间前缀中尾部的分隔符
			$prefix = mb_substr($class, 0, $pos + 1, 'UTF8');
			//剩余的就是相对类名称
			$relative_class = mb_substr($class, $pos + 1, null, 'utf8');
			
			//利用命令空间前缀和相对类名来加载映射文件
			$mapped_file = self::loadMappedFile($prefix, $relative_class);
			if ($mapped_file) {
				return $mapped_file;
			}
			
			//删除命名空间前缀尾部的分隔符，以便于下一下strrpos()迭代
			$prefix = trim($prefix, '\\');
		}
		
		//找不到相应文件
		return false;
	}
	
	
	/**
	 * 根据命名空间前缀和相对类来加载映射文件
	 * @param $prefix
	 * @param $relative_class
	 * @return bool|string
	 */
	public static function loadMappedFile($prefix, $relative_class)
	{
		//命名空间前缀有base目录吗
		if (isset(self::$prefixes[$prefix]) === false) {
			return false;
		}
		
		//遍历命名空间前缀的base目录
		foreach (self::$prefixes[$prefix] as $base_dir) {
			//用base 目录替代命名空间前缀,
			//在相对类名中用目录分隔符 '/' 来替换命名空间分隔符'\'    ,
			//并在后面追加.php 组成$file 绝对路径
			
			$file = $base_dir . str_replace('\\', DS, $relative_class) . '.php';
//			$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
			
			if (self::requireFile($file)) {
				return $file;
			}
		}
		return false;
	}
	
	//当文件存在，则从文件系统载入
	protected static function requireFile($file)
	{
		if (file_exists($file)) {
			require $file;
			return true;
		}
		return false;
	}
}























