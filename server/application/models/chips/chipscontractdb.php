<?php 
/**
 * @author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsContractDb extends CI_Model{
		private $tableName = 't_chips_contract';

		public function __construct(){
			parent::__construct();
		}

		public function getContractInfo($userId){
			$this->db->where('userId',$userId);
			$contractInfo = $this->db->get($this->tableName)->result_array();
			if($contractInfo){
				return $contractInfo[0];
			}else{
				return array(
					'count' => '',
					'data'  => '',
					);
			}
		}

		public function updateContract($userId,$data){
			$contractInfo = $this->db->where('userId',$userId)->get($this->tableName)->result_array();
			if($contractInfo){
				$this->db->where('userId',$userId);
				$this->db->update($this->tableName,$data);
			}else{
				$data['userId'] = $userId;
				$this->db->insert($this->tableName,$data);
			}
			return $this->db->affected_rows();
		}
	}
?>