<?php 
/**
 * @author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Address_model extends CI_Model{
		private $tableName = 't_address';

		public function __construct(){
			parent::__construct();
		}

		public function upOrAdd($clientId,$name,$province,$city,$address,$phone){
			$addressInfo = $this->db->select('addressId')->from($this->tableName)->where('clientId',$clientId)->get()->result_array();
			$addressId   = $addressInfo[0]['addressId'];
			$this->db->where('addressId',$addressId);
			$data['name'] = $name;
			$data['province'] = $province;
			$data['city'] = $city;
			$data['address'] = $address;
			$data['phone']   = $phone;
			$this->db->update($this->tableName,$data);
			return $addressId;
		}
	}
 ?>