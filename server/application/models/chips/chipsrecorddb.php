<?php 
/**
 * auchot:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsRecorddb extends CI_Model{
		private $tableName = 't_chips_record';

		public function __construct(){
			parent::__construct();
			// $this->load->database($this->tableName);
		}

		public function addRecord($clientId,$chips_id,$newprice,$num){
			$data['clientId'] = $clientId;
			$data['chips_id'] = $chips_id;
			$data['newprice'] = $newprice;
			$data['num']      = $num;
			$this->db->insert($this->tableName,$data);
			return $this->db->insert_id();
		}

		//增加众筹记录 7.1
		public function addChipsRecord($clientId,$chips_id,$chips_order_id){
			// $orderInfo = $this->db->select('num')->from('t_chips_order')->where('chips_order_id',$chips_order_id)->get()->result_array();
			$chipsInfo = $this->db->select('newprice,num')->from('t_chips')->where('chips_id',$chips_id)->get()->result_array();
			// $orderInfo = $orderInfo[0];
			$chipsInfo = $chipsInfo[0];
			$data['clientId'] = $clientId;
			$data['chips_id'] = $chips_id;
			$data['newprice'] = $chipsInfo['newprice'];
			$data['num']      = $chipsInfo['num'];
			$this->db->insert($this->tableName,$data);
		}

		public function checkPersonNum($chips_id){
			$this->db->distinct();
			$numInfo = $this->db->select('clientId')->from($this->tableName)->where('chips_id',$chips_id)->get()->result_array();
			// $sql = "SELECT distinct `clientId` from t_chips_record where chips_id = {$chips_id}";
			// $numInfo = $this->db->query($sql);
			return count($numInfo);
		}

		public function recordNum($chips_id){
			$recordInfo = $this->db->where('chips_id',$chips_id)->get($this->tableName)->result_array();
			return $recordInfo;
		}
	}
 ?>