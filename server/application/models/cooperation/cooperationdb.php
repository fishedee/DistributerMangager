<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CooperationDb extends CI_Model{

    private $tableName = 't_cooperation';

    public function __construct(){
        parent::__construct();
    }

    public function add($userId,$data){
        // $insertData['userId'] = $userId;
        // $insertData['cooperationData'] = json_encode($data);
        $data['userId'] = $userId;
        $this->db->insert($this->tableName,$data);
        return $this->db->insert_id();
    }

    public function search($dataWhere,$limit){
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"])){
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
		}
		foreach ($dataWhere as $key => $value) {
			if($key == 'userId'){
				$this->db->where('userId',$dataWhere['userId']);
			}else{
				// if($value){
				// 	if(preg_match("/[\x7f-\xff]/", $value)){
				// 		$like = $key ."\":". substr(json_encode($value), 0,-1);
				// 	}else{
				// 		$like = $key ."\":\"". $value;
				// 	}
				// 	$this->db->like('cooperationData',$like,'both');
				// }
				$this->db->like($key,$value,'both');
			}
		}
		$orderInfo = $this->db->get($this->tableName)->result_array();
		$count = $this->db->where('userId',$dataWhere['userId'])->count_all_results($this->tableName);
		return array(
			'count' => $count,
			'data'  => $orderInfo
			);
	}

	public function searchAll(){
		return $this->db->get($this->tableName)->result_array();
	}
}
