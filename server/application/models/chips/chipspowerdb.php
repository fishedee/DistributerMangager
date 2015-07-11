<?php 
/**
 * @author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsPowerDb extends CI_Model{
		private $tableName = 't_chips_power';
		public function __construct(){
			parent::__construct();
		}

		//更改power
		public function changePower($clientId,$chips_id,$status){
			if($status == 1){
				//add
				$data['clientId'] = $clientId;
				$data['chips_id'] = $chips_id;
				$result = $this->db->select('powerId')->from($this->tableName)->where($data)->get()->result_array();
				if(!$result){
					$this->db->insert($this->tableName,$data);
				}
			}elseif($status == 0){
				//delete
				$this->db->delete($this->tableName,array('clientId'=>$clientId,'chips_id'=>$chips_id));
			}
			return $this->db->affected_rows();
		}

		//判断权限
		public function judge($clientId,$chips_id){
			$condition['clientId'] = $clientId;
			$condition['chips_id'] = $chips_id;
			$result = $this->db->select('powerId')->from($this->tableName)->where($condition)->get()->result_array();
			return count($result);
		}

		public function addPower($clientId,$chips_id){
			$condition['clientId'] = $clientId;
			$condition['chips_id'] = $chips_id;
			$result = $this->db->select('powerId,status')->from($this->tableName)->where($condition)->get()->result_array();
			if($result){
				if($result[0]['status'] != 1){
					$powerId = $result[0]['powerId'];
					$this->db->where('powerId',$powerId);
					$this->db->update($this->tableName,array('status'=>1));
				}
			}else{
				$data['clientId'] = $clientId;
				$data['chips_id'] = $chips_id;
				$data['status']   = 1;
				$this->db->insert($this->tableName,$data);
			}
		}
	}
 ?>