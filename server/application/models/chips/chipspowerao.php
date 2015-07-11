<?php 
/**
 * @author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsPowerAo extends CI_Model{

		public function __construct(){
			parent::__construct();
			$userId = $this->session->userdata('userId');
			$this->userId = $userId;
			if(!$userId){
				echo '非法操作';die;
			}
			$this->load->model('chips/chipsPowerDb','powerDb');
		}

		public function changePower($clientId,$chips_id,$status){
			return $this->powerDb->changePower($clientId,$chips_id,$status);
		}

		public function judge($clientId,$chips_id){
			return $this->powerDb->judge($clientId,$chips_id);
		}
	}
?>