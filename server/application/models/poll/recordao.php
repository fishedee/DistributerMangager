<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RecordAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('poll/recordDb','recordDb');
	}

	//检测报名
	public function checkVote($userId,$clientId){
		return $this->recordDb->checkVote($userId,$clientId);
	}

	//投票
	public function vote($userId,$clientId){
		$data['userId'] = $userId;
		$data['clientId'] = $clientId;
		return $this->recordDb->add($data);
	}

}
