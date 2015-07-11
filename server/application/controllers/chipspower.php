<?php 
/**
 * @author:zzh
 */
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsPower extends CI_Controller{
		private $userId;

		public function __construct(){
			parent::__construct();
			$userId = $this->session->userdata('userId');
			$this->userId = $userId;
			if(!$userId){
				echo '非法操作';die;
			}
			$this->load->model('chips/chipsAo','chipsAo');
			$this->load->model('client/client_model','client');
			$this->load->model('chips/chipsPowerAo','powerAo');
			$this->load->library('argv','argv');
		}
		
		/**
		 * @view json
		 */
		public function getUser(){

			$dataLimit = $this->argv->checkGet(array(
				array('pageIndex','require'),
				array('pageSize','require'),
			));
			$chips_id = $_GET['chips_id'];
			$result = $this->client->getClient($this->userId,$dataLimit,$chips_id);
			// var_dump($result);die;
			return $result;
		}

		//更改权限
		public function changePower(){
			if($this->input->is_ajax_request()){

		        $userId = $this->session->userdata('userId');

		        $clientId = $this->input->post('clientId');
		        $chips_id = $this->input->post('chips_id');
		        $status   = $this->input->post('status');

		        $result   = $this->powerAo->changePower($clientId,$chips_id,$status);
		        if($result){
		        	echo 1;
		        }else{
		        	echo 0;
		        }
			}
		}

		//判断
		public function judge(){
			if($this->input->is_ajax_request()){
				$argv = $this->argv->check(array(
		            array('userId', 'require')
		        ));
		        $userId = $argv['userId'];
		        $clientId = $this->session->userdata('clientId');
		        $chips_id = $this->input->post('chips_id');
		        $chipsInfo= $this->chipsAo->chipsDetail($chips_id,$userId);
		        $result   = $this->powerAo->judge($clientId,$chips_id);
		        if($result){
		        	// echo json_encode($chipsInfo['password']);
		        	echo json_encode($chipsInfo['mobilePassword']);
		        }else{
		        	echo 0;
		        }
			}
		}
	}
 ?>