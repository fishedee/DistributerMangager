<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class PostControllerConstructorHook{
	function setUserId(){
		$host = $_SERVER['HTTP_HOST'];
		preg_match('/^(\d+)\./',$host,$matches);
		if(count($matches) == 0 ){
			return;
		}
		$_GET['userId'] = $matches[1];
		$_POST['userId'] = $matches[1];
	}
}