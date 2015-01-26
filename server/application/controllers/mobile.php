<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mobile extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/userAo','userAo');
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->library('argv','argv');
    }
	/**
	* @view
	*/
	public function file($userId,$url1='',$url2='',$url3='')
	{
		$url = '';
		if( $url1 != '')
			$url .= '/'.$url1;
		if( $url2 != '')
			$url .= '/'.$url2;
		if( $url3 != '')
			$url .= '/'.$url3;
		if( $url == '')
			$url = '/company.html';
		
		//³¢ÊÔ´¿¾²Ì¬ÎÄ¼ş
		$staticAddress = dirname(__FILE__).'/../../../static/build/mobile';
		if( file_exists($staticAddress.$url) ){
			require_once($staticAddress.$url);
			return;
		}
			
		//³¢ÊÔÄ£°å¾²Ì¬ÎÄ¼ş
		$staticAddress = dirname(__FILE__).'/../../../static/build/mobile/template/sample1';
		if( file_exists($staticAddress.$url) ){
			require_once($staticAddress.$url);
			return;
		}
		
		//·ñÔò404
		show_404();
	}
	
}
