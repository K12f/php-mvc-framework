<?php
namespace home\controller;

use core\Controller;

class HelloController extends Controller
{
	public function hello()
	{
		$this->assign('hello','world');
		$this->display();
	}
}
