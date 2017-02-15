<?php
namespace core;

class Config
{
	private static $config = [];
	
	public static function get($name = null)
	{
		if (empty($name)) {
			return self::$config;
		}
		return isset(self::$config[strtolower($name)]) ? self::$config[strtolower($name)] : null;
	}
	
	public static function set($name, $value = null)
	{
		if (is_string($name)) {
			self::$config[strtolower($name)] = $value;
		} elseif (is_array($name)) {
			
			//是数组，循环设置
			if (!empty($value)) {
				self::$config[$value] = isset(self::$config[$value]) ? array_merge(self::$config[$value]) : self::$config[$value] = $name;
			} else {
				return self::$config = array_merge(self::$config, array_change_key_case($name));
			}
		}
		return self::$config;
	}
	
	public static function has($name)
	{
		return isset(self::$config[strtolower($name)]);
	}
	
	//加载其他配置文件
	public static function load($file)
	{
		if (is_file($file)) {
			$type = pathinfo($file, PATHINFO_EXTENSION);
			if ($type !== 'php') {
				return self::$config;
			} else {
				return self::set(include $file);
			}
		} else {
			return self::$config;
		}
	}
}