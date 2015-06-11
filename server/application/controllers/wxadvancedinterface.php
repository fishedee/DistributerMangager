<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wxadvancedinterface extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->model('weixin/wxMenuAo','wxMenuAo');
		$this->load->library('argv','argv');
	}
	
	/**
	 * @view json
	 * 获取自定义菜单
	 */
	
	public function getMenu()
	{
		//return '';die();
		$userId = $this->loginAo->checkMustLogin();
		$userId =$userId['userId'];
		
		//检查权限
		$userApp = $this->userAppAo->get($userId);
		$this->userAppAo->check($userApp);
	
		//执行业务逻辑
		return  $data=$this->wxMenuAo->getSetting($userId);
		
		//print_r($data);die();
	}
	
	/**
	 * @view json
	 * 设置自定义菜单
	 */
	public function setMenu(){
		//检查输入参数
		if (!empty($_POST['name1'])){$data[0]['name']=$this->input->post('name1');}
		if (!empty($_POST['url1'])){$data[0]['url']=$this->input->post('url1');}
		if (!empty($_POST['name2'])){$data[1]['name']=$this->input->post('name2');}
		if (!empty($_POST['url2'])){$data[1]['url']=$this->input->post('url2');}
		if (!empty($_POST['name3'])){$data[2]['name']=$this->input->post('name3');}
		if (!empty($_POST['url3'])){$data[2]['url']=$this->input->post('url3');}
		if (!empty($_POST['sub_button1'])){$data[0]['sub_button'] = $this->input->post('sub_button1');}
 		if (!empty($_POST['sub_button2'])){$data[1]['sub_button'] = $this->input->post('sub_button2');}
 		if (!empty($_POST['sub_button3'])){$data[2]['sub_button'] = $this->input->post('sub_button3');}
//  		if (!empty($_POST['name1'])){$data[0]['type']='view';}
//  		if (!empty($_POST['name2'])){$data[1]['type']='view';}
//  		if (!empty($_POST['name3'])){$data[2]['type']='view';}
 		
 		//存入数据库的数据
 		$mysqlData = $this->argv->checkPost(array(
 				array('name1','require'),
 				array('url1','require'),
 				array('name2','require'),
 				array('url2','require'),
 				array('name3','require'),
 				array('url3','require'),
 		));
 		
 		if (!empty($_POST['sub_button1'])){$mysqlData['sub_button1'] = $this->argv->checkPost(array(array('sub_button1','option',array())))['sub_button1'];}
		if (!empty($_POST['sub_button2'])){$mysqlData['sub_button2'] = $this->argv->checkPost(array(array('sub_button2','option',array())))['sub_button2'];}
		if (!empty($_POST['sub_button3'])){$mysqlData['sub_button3'] = $this->argv->checkPost(array(array('sub_button3','option',array())))['sub_button3'];}
		
 		//$data2=json_encode($data,JSON_UNESCAPED_UNICODE);//测试用的
 		//print_r($mysqlData);die();
 		
		//检查权限
		$userId = $this->loginAo->checkMustLogin();
		$userId =$userId['userId'];
		
		//执行业务
		$this->wxMenuAo->setSetting($userId,$data,$mysqlData);	
 		
	}
}