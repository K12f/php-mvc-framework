<?php

namespace core;

use core\Config;

use PDO;

class Model
{
	protected $db;
	protected $table;
	
	public function __construct($table = '')
	{
		$this->db = new PDO('mysql:host=' . Config::get('db_host') . ';dbname=' . Config::get('db_name') . ';charset=' . Config::get('db_charset'), Config::get('db_user'), Config::get('db_pwd'));
		$this->table = Config::get('db_table_prefix') . $table;
	}
	
	public function __destruct()
	{
		$this->free();
	}
	//获取数据表字段
	public function getFields()
	{
		$sql = 'SHOW COLUMNS FROM `' . $this->table . '`';  //拼接数据库
		$pdo = $this->db->query($sql);
		$result = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$info = [];
		if ($result) {
			foreach ($result as $key => $value) {
				$value = array_change_key_case($key);
				$info[$value['field']] =
					[
						'name' => $value['field'],
						'type' => $value['type'],
						'notnull' => (bool)('' === $value['null']),
						'default' => $value['default'],
						'primary' => (strtolower($value['key']) == 'pri'),
						'auto' => (strtolower($value['extra']) == 'auto_increment'),
					];
			}
		}
		return $info;
	}
	
	//获取数据库中所有表
	public function getTables()
	{
		$sql = "SHOW TABLES";
		$pdo = $this->db->query($sql);
		$result = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$info = [];
		foreach ($result as $key => $val) {
			$info['key'] = current($val);
		}
		return $info;
	}
	
	//释放连接
	public function free()
	{
		$this->db = null;
	}
	
	//获取客户端真实ip地址
	protected function getIP()
	{
		if (getenv('HTTP_CLIENT_TIP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknow')) {
			$ip = getenv('REMOTE_ADDR');
		} elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknow')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip = 'unknow';
		}
		return $ip;
	}
	
	//新增数据
	public function save($data = [])
	{
		$keys = '';
		$values = '';
		foreach ($data as $key => $value) {
			$keys .= "$key,";
			$values .= "'" . $value . "',";
		}
		$keys = mb_substr($keys, 0, mb_strlen($keys, 'utf8') - 1, 'utf8');
		$values = mb_substr($values, 0, mb_strlen($values, 'utf8') - 1);
		$sql = 'INSERT INTO `' . $this->table . '` (' . $keys . ') VALUES (' . $values . ')';
		$pdo = $this->db->query($sql);
		if ($pdo) {
			return true;
		} else {
			$this->log_error('save error', $sql);
			return false;
		}
		
	}
	
	//更新数据
	public function update($data = [], $wheres = [], $options = 'and')
	{
		$keys = '';
		$where = '';
		foreach ($data as $key => $value) {
			$keys .= $key . "= '" . $value . "',";
		}
		if (count($wheres) > 1) {
			foreach ($wheres as $k => $v) {
				$where .= $k . "='" . $v . "' " . $options . " ";
			}
			$where = mb_substr($where, 0, mb_strlen($where, 'utf8') - mb_strlen($options, 'utf8') - 2, 'utf8');
		} else {
			foreach ($wheres as $k => $v) {
				$where .= $k . "='" . $v . "'";
			}
		}
		$keys = mb_substr($keys, 0, mb_strlen($keys, 'utf8') - 1, 'utf8');
		$sql = "UPDATE " . $this->table . 'SET ' . $keys . ' WHERE ' . $where;
		$pdo = $this->db->query($sql);
		if ($pdo) {
			return true;
		} else {
			$this->log_error('update_error', $sql);
			return false;
		}
	}
	
	//查找数据
	public function select($fields, $wheres = [], $options = 'and')
	{
		$field = '';
		if (is_string($fields)) {
			$field = $fields;
		} elseif (is_array($fields)) {
			foreach ($fields as $k => $v) {
				$field .= $v . ',';
			}
			$field = mb_substr($field, 0, mb_strlen($field, 'utf8') - 1);
		}
		$where = '';
		foreach ($wheres as $k => $v) {
			$where .= $k . ' ' . $options . "'{$v}',";
		}
		$where = mb_substr($where, 0, mb_strlen($where, 'utf8') - 1, 'utf8');
		$sql = "SELECT " . $field . ' FROM ' . $this->table . ' WHERE ' . $where;
		$pdo = $this->db->query($sql);
		if ($pdo) {
			$result = $pdo->fetchAll(PDO::FETCH_ASSOC);
			return $result;
		} else {
			$this->log_error('select error', $sql);
			return false;
		}
	}
	
	public function delete($wheres = [], $options = 'and')
	{
		$where = '';
		foreach ($wheres as $key => $value) {
			$where .= $key . ' ' . $options . " '$value',";
		}
		$where = substr($where, 0, strlen($where) - 1);
		$sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $where;
		$pdo = $this->db->query($sql);
		if ($pdo) {
			return true;
		} else {
			$this->log_error('delete error', $sql);
			return false;
		}
	}
	
	protected function log_error($message = '', $sql = '')
	{
		$ip = $this->getIP();
		$time = (new \DateTime(null, new \DateTimeZone('Asia/ShangHai')))->format('Y-m-d H:i:s');
		$message = $message . PHP_EOL . "$sql" . PHP_EOL . "客户IP:$ip" . PHP_EOL . "时间:$time" . PHP_EOL . PHP_EOL;
		$server_date = (new \DateTime(null, new \DateTimeZone('Asia/ShangHai')))->format('Y-m-d');
		$filename = $server_date . "_SQL.txt";
		$file_path = RUNTIME_PATH . 'log' . DS . $filename;
		$error_content = $message;
		$file = RUNTIME_PATH . 'log'; //设置文件保存目录
		
		//建立文件夹
		if (!file_exists($file)) {
			if (!mkdir($file, 0777)) {
				die('upload files directory does not exist and creation failed');
			}
		}
		
		//建立txt日期文件
		if (!file_exists($file_path)) {
			fopen($file_path, 'w+');
			if (is_writeable($file_path)) {
				if (!$handle = fopen($file_path, 'a')) {
					echo 'cannot open $filename';
					exit;
				}
				
				if (!fwrite($handle, $error_content)) {
					echo 'Cannot write ' . $filename;
					exit();
				}
				echo 'error logging is saved';
				fclose($handle);
			} else {
				echo 'file ' . $filename . ' cannot write';
			}
		} else {
			if (is_writable($file_path)) {
				if (!$handle = fopen($file_path, 'a')) {
					echo 'Cannot open ' . $filename;
					exit();
				}
				if (!fwrite($handle, $error_content)) {
					echo "Cannot write " . $filename;
					exit();
				}
				echo '---Error logging is saved!!!';
				fclose($handle);
			}else {
				echo 'file ' . $filename . ' cannot write';
			}
		}
	}
}