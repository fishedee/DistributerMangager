<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionConfigAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('distribution/distributionConfigDb','distributionConfigDb');
	}

	//获取分销配置
	public function getConfig($userId){
		$config = $this->distributionConfigDb->getConfig($userId);
		if($config){
			return $config[0];
		}else{
			return 0;
		}
	}

	//提交修改
	public function sub($userId,$data){
		$config = $this->getConfig($userId);
		foreach ($data as $key => $value) {
			if(!$value && $key != 'distribution'){
				unset($data[$key]);
			}
		}
		if($config){
			return $this->distributionConfigDb->mod($userId,$data);
		}else{
			$data['userId'] = $userId;
			return $this->distributionConfigDb->add($data);
		}
	}
}
