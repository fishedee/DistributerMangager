<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WxMaterialDb extends CI_Model 
{
	var $tableName = "t_weixin_material";
	var $field = array(
			'materialId'=>'',
			'weixinSubscribeId'=>'',
			'Title'=>'',
			'Description'=>1,
			'Url'=>'',
			'PicUrl'=>'',
			'sort'=>''
	);
	public function __construct(){
		parent::__construct();
	}

	public function getByWeixinSubscribeId($weixinSubscribeId){
		$this->db->where("weixinSubscribeId",$weixinSubscribeId);
		return $this->db->get($this->tableName)->result_array();
	}
	
	public function addBatch( $data ){
		if( count($data) == 0 )
			return;
		foreach( $data as $key=>$singleData ){
			$data[$key] = array_intersect_key($data[$key],$this->field);
		}
		$this->db->insert_batch($this->tableName,$data);
	}
	
	/*删除被关注内容*/
	//第二参数，更新文件状态
	public function del($weixinSubscribeId,$data){
		$this->db->where("weixinSubscribeId",$weixinSubscribeId);
		$this->db->select('PicUrl');
		$Material=$this->db->get($this->tableName)->result_array();
		
		//更新文件就启动，被触动方法有graphicMod
		if (is_array($data)){
		foreach ($data['graphic'] as $v){
				$newImg[]=$v['PicUrl'];
			}
		foreach ($Material as $k){
			$oldImg[]=$k['PicUrl'];
		}
		//找出差值
		$chaImg=array_diff_assoc($oldImg,$newImg);
		//差值是否在旧图片数组上
		foreach ($chaImg as $v){
			if(in_array($v, $oldImg)){
				if(!in_array($v,$newImg)){
				//要删除的图片
				@unlink(dirname(__FILE__).'/../../../..'.$v);;
				}
			}
		}
		}else {
			//删除图片
			if(is_array($Material)){
			foreach ($Material as $k){
				@unlink(dirname(__FILE__).'/../../../..'.$k['PicUrl']);
			}
			}
		}
		$this->db->delete($this->tableName, array('weixinSubscribeId' => $weixinSubscribeId));
	}
}
?>