<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
	    $this->load->model('distribution/distributionDb', 'distributionDb');
        $this->load->model('distribution/distributionstateEnum', 'distributionStateEnum');
    }

    public function search($where, $limit){
        return $this->distributionDb->search($where, $limit);
    }

    private function check($upUserId, $downUserId){
        return 0;
    }

    public function get($userId, $distributionId){
        $distribution = $this->distributionDb->get($distributionId);
        if($distribution['upUserId'] != $userId && $distribution['downUserId'] != $userId)
            throw new CI_MyException(1, "无权查询此非本用户的分成关系");
        else
            return $distribution;
    }
    public function add($upUserId, $downUserId, $data){
        $this->check($upUserId, $downUserId);
        $this->distributionDb->add($upUserId, $downUserId, $data); 
    }

    public function del($userId, $distributionId){
        $distribution = $this->get($userId, $distributionId);
        $this->distributionDb->del($distribution['distributionId']);
    }

    public function mod($distributionId, $data){
        $this->distributionDb->mod($distributionId, $data);
    }
    

    private $path = array();
    private $result_path = array();
    
    private function dfs($originUserId, $userId){
	if($originUserId == $userId){
		$this->result_path = $this->path;
		return;
	}

	$where = array(
		'upUserId'=>$originUserId,
        'state'=>$distributionStateEnum->ON_ACCEPT
	);
	$response = $this->search($where, array());
	$distributions = $response['data'];
	foreach($distributions as $distribution){
		log_message('error', $distribution['downUserId']);
		log_message('error', $userId);
		$this->path[$distribution['downUserId']] = 1;
		$this->dfs($distribution['downUserId'], $userId);	
		unset($this->path[$distribution['downUserId']]);
	}
    }

    public function getLink($originUserId, $userId){
        $this->path = array(); 
	$this->result_path = array();
	$this->path[$originUserId] = 1;
        $this->dfs($originUserId, $userId);
	foreach($this->result_path as $key=>$value)
		$map[] = $key;
        return $map;
    }
}

