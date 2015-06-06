<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WxSubscribeDb extends CI_Model 
{
	var $tableName = "t_weixin_subscribe";
	var $field = array(
			'materialClassifyId'=>'',
			'weixinSubscribeId'=>'',
			'userId'=>'',
			'title'=>'',
			'remark'=>'',
			'isRelease'=>'',
	);
	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "title" || $key == "remark")
				$this->db->like($key,$value);
			else if( $key == "userId" || $key == "isRelease")
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "title" || $key == "remark")
				$this->db->like($key,$value);
			else if( $key == "userId" || $key == "isRelease")
				$this->db->where($key,$value);
		}
			
		$this->db->order_by('weixinSubscribeId','asc');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get($this->tableName)->result_array();
		return array(
			"count"=>$count,
			"data"=>$query
		);
	}
	
	public function graphicGet($luckyDrawId){
		$this->db->where("weixinSubscribeId",$luckyDrawId);
		$query = $this->db->get($this->tableName)->result_array();
		if( count($query) == 0 )
			throw new CI_MyException(1,'不存在此图文信息');
		return $query[0];
	}
	
	public function add($data){
	
		$data = array_intersect_key($data,$this->field);
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}
	
	/*发布被关注内容*/
	public function publish($userId,$weixinSubscribeId){
		//先全部设置为未发布
		$this->db->where("userId", $userId);
		$data=array('isRelease'=>1);
		$this->db->update($this->tableName, $data);
		//后单独那个发布
		$this->db->where("userId", $userId);
		$this->db->where("weixinSubscribeId", $weixinSubscribeId);
		$data=array('isRelease'=>2);
		$this->db->update($this->tableName, $data);
	}
	
	/*删除被关注内容*/
	public function del($userId,$weixinSubscribeId){
		$this->db->delete($this->tableName, array('userId' => $userId,'weixinSubscribeId' => $weixinSubscribeId)); 
	}
}
?>