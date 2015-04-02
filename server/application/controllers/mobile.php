<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mobile extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/userAo','userAo');
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->model('template/companyTemplateAo','companyTemplateAo');
		$this->load->library('argv','argv');
    }
	/**
	* @view
	*/
	public function file($userInfo,$url1='',$url2='',$url3='')
	{
		$userId = $_GET['userId'];
		$url = '';
		if( $url1 != '')
			$url .= '/'.$url1;
		if( $url2 != '')
			$url .= '/'.$url2;
		if( $url3 != '')
			$url .= '/'.$url3;
		if( $url == ''){
			header("Location: /$userInfo/company.html");
		}
		//根据后缀名输出Content-Type
		$pos = strripos($url,'.');
		if( $pos != false )
			$extension = strtolower(substr($url,$pos+1));
		else
			$extension = '';
		if( $extension == 'html' || $extension == 'htm' )
			header( 'Content-Type:text/html');
		else if( $extension == 'css' )
			header( 'Content-Type:text/css');
		else if( $extension == 'js' )
			header('Content-type: text/javascript');  
		else if( $extension == 'png')
			header("Content-Type: image/png");
		else if( $extension == 'gif')
			header("Content-type: image/gif");
		else 
			header('Content-type: image/jpeg');
		
		
		//尝试纯静态文件
		$staticAddress = dirname(__FILE__).'/../../../static/build/mobile';
		if( file_exists($staticAddress.$url) ){
			ob_clean();  
			flush();  
			readfile($staticAddress.$url);  
			return;
		}
			
		//尝试模板静态文件
		$companyTemplateId = $this->companyTemplateAo->getByUserId($userId);
		if( $companyTemplateId != 0 ){
			$template = $this->companyTemplateAo->get($companyTemplateId);
			$staticAddress = dirname(__FILE__).'/../../../'.$template['url'];
			if( file_exists($staticAddress.$url) ){
				ob_clean();  
				flush();  
				readfile($staticAddress.$url);  
				return;
			}
		}
		
		//否则404
		ob_clean();  
		flush();  
		readfile($staticAddress.'/error.html');  
	}
	
}
