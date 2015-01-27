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
	public function file($userId,$url1='',$url2='',$url3='')
	{
		$url = '';
		if( $url1 != '')
			$url .= '/'.$url1;
		if( $url2 != '')
			$url .= '/'.$url2;
		if( $url3 != '')
			$url .= '/'.$url3;
		if( $url == ''){
			header("Location: /$userId/company.html");
		}
		
		//≥¢ ‘¥øæ≤Ã¨Œƒº˛
		$staticAddress = dirname(__FILE__).'/../../../static/build/mobile';
		if( file_exists($staticAddress.$url) ){
			require_once($staticAddress.$url);
			return;
		}
			
		//≥¢ ‘ƒ£∞Âæ≤Ã¨Œƒº˛
		$companyTemplateId = $this->companyTemplateAo->getByUserId($userId);
		if( $companyTemplateId != 0 ){
			$template = $this->companyTemplateAo->get($companyTemplateId);
			$staticAddress = dirname(__FILE__).'/../../../'.$template['url'];
			if( file_exists($staticAddress.$url) ){
				require_once($staticAddress.$url);
				return;
			}
		}
		
		//∑Ò‘Ú404
		show_404();
	}
	
}
