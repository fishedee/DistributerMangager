<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LuckyDrawClientDb extends CI_Model 
{
	var $tableName = "t_lucky_draw_client";

	public function __construct(){
		parent::__construct();
	}

	public function getByLuckyDrawId($luckyDrawId){
		$this->db->where("luckyDrawId",$luckyDrawId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function getByLuckyDrawAndClientId($luckyDrawId,$clientId){
		$this->db->where("luckyDrawId",$luckyDrawId);
		$this->db->where("clientId",$clientId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function getByClientId($clientId){
		$this->db->where("clientId",$clientId);
		return $this->db->get($this->tableName)->result_array();
	}
	
	public function add( $data ){
		$this->db->insert($this->tableName,$data);
	}

}
