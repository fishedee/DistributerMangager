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
			else if( $key == "userId" || $key == "isRelease" || $key == "weixinSubscribeId")
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "title" || $key == "remark")
				$this->db->like($key,$value);
			else if( $key == "userId" || $key == "isRelease" || $key == "weixinSubscribeId")
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


	public function getKeyResponseId($userId){
		return $this->db->select('weixinSubscribeId')->from($this->tableName)->where('userId',$userId)->get()->result_array();
	}

	/*增加关键字回复*/
	public function addKey($data){
		$this->db->insert('t_weixin_key_response',$data);
		return $this->db->insert_id();
	}

	/*更新*/
	public function updateKey($keyResponseId,$data){
		$this->db->where('keyResponseId',$keyResponseId);
		$this->db->update('t_weixin_key_response',$data);
		return $this->db->affected_rows();
	}

	public function keySearch($dataWhere,$dataLimit){
		$countInfo = $this->db->where($dataWhere)->get('t_weixin_key_response')->result_array();
		$count     = count($countInfo);
		if(isset($limit["pageIndex"]) && isset($limit["pageSize"])){
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
		}
		$keyInfo = $this->db->where($dataWhere)->get('t_weixin_key_response')->result_array();
		return array(
			'count' => $count,
			'data'  => $keyInfo
			);
	}

	/*删除关键词自动回复*/
	public function keyResponseDel($keyResponseId){
		$this->db->delete('t_weixin_key_response',array('keyResponseId'=>$keyResponseId));
		return $this->db->affected_rows();
	}

	/*获取详细信息*/
	public function getKeyResponseInfo($keyResponseId){
		$this->db->where('keyResponseId',$keyResponseId);
		$info =  $this->db->get('t_weixin_key_response')->result_array()[0];
		$this->db->where('weixinSubscribeId',$info['weixinSubscribeId']);
		$subInfo = $this->db->get($this->tableName)->result_array();
		switch ($subInfo[0]['materialClassifyId']) {
			case 1:
				$subInfo[0]['materialClassifyId'] = '多图文';
				break;
			case 2:
				$subInfo[0]['materialClassifyId'] = '单图文';
				break;
			case 3:
				$subInfo[0]['materialClassifyId'] = '文字';
				break;
		}
		$info['weixinSubscribeId'] = $subInfo;
		return $info;
	}

	/*根据关键字查询*/
	public function keyWordSearch($keyword){
		$condition['keyWord'] = $keyword;
		// $this->db->where($condition);
		// $result = $this->db->get('t_weixin_key_response')->result_array();
		$result = $this->db->select('weixinSubscribeId')->from('t_weixin_key_response')->where($condition)->get()->result_array();
		return $result[0]['weixinSubscribeId'];
	}

	/*根据素材id获取素材类型*/
	public function materialClassifyIdSearch($weixinSubscribeId){
		$condition['weixinSubscribeId'] = $weixinSubscribeId;
		$result = $this->db->select('materialClassifyId')->from($this->tableName)->where($condition)->get()->result_array();
		return $result[0]['materialClassifyId'];
	}

	/*根据素材id查询素材内容*/
	public function materialSearch($weixinSubscribeId){
		$condition['weixinSubscribeId'] = $weixinSubscribeId;
		$this->db->where($condition);
		return $this->db->get('t_weixin_material')->result_array();
	}
}
?>