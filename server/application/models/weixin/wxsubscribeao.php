<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WxSubscribeAo extends CI_Model {
	public function __construct(){
		parent::__construct();
 		$this->load->model('weixin/wxSubscribeDb','wxSubscribeDb');
 		$this->load->model('weixin/wxMaterialDb','wxMaterialDb');
 		$this->load->model('weixin/wxSubscribeEnum','wxSubscribeEnum');
 		$this->load->model('weixin/wxSubscribeStateEnum','wxSubscribeStateEnum');
	}
	
	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->wxSubscribeDb->search($dataWhere,$dataLimit);
	}
	
	public function graphicGet($userId,$weixinSubscribeId){
		//拉多图文信息
		$graphic = $this->wxSubscribeDb->graphicGet($weixinSubscribeId);
		if( $graphic['userId'] != $userId )
			throw new CI_MyException(1,'没有权限查看此素材');
		$graphic['graphic'] = $this->wxMaterialDb->getByWeixinSubscribeId($weixinSubscribeId);
	
		return $graphic;
	}
	
	/*微信多图文内容搜索*/
	public function graphicSearch($userId,$weixinSubscribeId){
		//拉多图文信息
		$graphic_s= $this->wxMaterialDb->getByWeixinSubscribeId($weixinSubscribeId);
	
		return $graphic_s;
	}
	
	public function graphicAdd($userId,$data){
		
		//添加Subscribe列表
		$data['userId'] = $userId;
		$data['materialClassifyId'] = $this->wxSubscribeEnum->GRAPHIC;
		$data['isRelease']= '1';//未发布状态
		$weixinSubscribeId = $this->wxSubscribeDb->add($data);
		
		//添加图文
		$material = array();
		$sort = 1 ;
		foreach( $data['graphic'] as $singleMaterial ){
			$material[] = array_merge(
					$singleMaterial,
					array(
							'weixinSubscribeId'=>$weixinSubscribeId,
							'sort'=>$sort++
					)
			);
		}
		
		$this->wxMaterialDb->addBatch($material);
	
		return $weixinSubscribeId;
	}
	
	/*发布被关注内容*/
	public function publish($userId,$weixinSubscribeId){
		$this->wxSubscribeDb->publish($userId,$weixinSubscribeId);
	}
	
	/*删除被关注内容*/
	public function del($userId,$weixinSubscribeId,$data){
		$this->wxSubscribeDb->del($userId,$weixinSubscribeId);
		$this->wxMaterialDb->del($weixinSubscribeId,$data);//第二参数，更新文件状态
	}

	
	}
?>