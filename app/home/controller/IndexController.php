<?php

namespace home\controller;

use core\Controller;
use home\model\IndexModel;

class IndexController extends Controller
{
	public function index($id=1,$name='')
	{
		echo 'adfasd' . $id;
		echo $name;
		$this->assign('name',$name);
		$this->display();
//		$model = new IndexModel();
//		if ($model->save(['name'=>'hello','password'=>'shiyanlou'])) {
//			$res = $model->select('name',['id'=>0],'>');
//			($res);
//		} else {
//			echo 'Failed';
//		}
	}
	
	public function hello()
	{
		echo 'hello';
	}
	
	public function world()
	{
		echo 'world';
	}
}