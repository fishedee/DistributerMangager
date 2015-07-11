<?php 
/**
 * @author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsContract extends CI_Controller{
		private $userId;

		public function __construct(){
			parent::__construct();
			//加载各类需要的模型
			$this->load->model('user/user_model','usermodel');
			$this->load->model('chips/chipsContractDb','contract');
			$this->load->library('argv', 'argv');
			$userId = $this->session->userdata('userId');
			$this->userId = $userId;
			if(!$userId){
				echo '非法操作';die;
			}
			$userIdInfo = $this->usermodel->checkUserId($userId);
			if(empty($userIdInfo)){
				echo '非法操作';die;
			}
		}

		/**
		 * @view json
		 */
		public function getContractInfoBack(){
			if($this->input->is_ajax_request()){
				return $this->contract->getContractInfo($this->userId);
			}
		}

		public function getContractInfo(){
			if($this->input->is_ajax_request()){
				$argv = $this->argv->check(array(
		            array('userId', 'require')
		        ));
		        $userId = $argv['userId'];
				echo json_encode($this->contract->getContractInfo($userId));
			}
		}

		public function updateContract(){
			if($this->input->is_ajax_request()){
				$data = $this->input->post('data');
				$result = $this->contract->updateContract($this->userId,$data);
				if($result){
					echo 1;
				}else{
					echo 0;
				}
			}
		}
	}
 ?>