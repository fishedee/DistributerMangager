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
		//拉取素材资料
		$graphic = $this->wxSubscribeDb->graphicGet($weixinSubscribeId);
		if( $graphic['userId'] != $userId )
			throw new CI_MyException(1,'没有权限查看此素材');
		if(strstr($_SERVER['HTTP_REFERER'], 'graphic')){
			$graphic['graphic'] = $this->wxMaterialDb->getByWeixinSubscribeId($weixinSubscribeId);
		}elseif(strstr($_SERVER['HTTP_REFERER'], 'singleGraphic')){
			$materialData=$this->wxMaterialDb->getByWeixinSubscribeId($weixinSubscribeId)[0];
			$graphic=array_merge($graphic,$materialData);
		}elseif(strstr($_SERVER['HTTP_REFERER'], 'theText')){
			$materialData=$this->wxMaterialDb->getByWeixinSubscribeId($weixinSubscribeId)[0];
			$graphic=array_merge($graphic,$materialData);
		}
		
	
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
		if(strstr($_SERVER['HTTP_REFERER'], 'graphic')){
			$data['materialClassifyId'] = $this->wxSubscribeEnum->GRAPHIC;
		}elseif(strstr($_SERVER['HTTP_REFERER'], 'singleGraphic')){
			$data['materialClassifyId'] = $this->wxSubscribeEnum->SINGLEGRAPHIC;
		}elseif(strstr($_SERVER['HTTP_REFERER'], 'theText')){
			$data['materialClassifyId'] = $this->wxSubscribeEnum->THETEXT;
		}
		$data['isRelease']= '1';//未发布状态
		$weixinSubscribeId = $this->wxSubscribeDb->add($data);
		
		//添加多图文
		$material = array();
		$sort = 1 ;
		if(strstr($_SERVER['HTTP_REFERER'], 'graphic')){
				foreach( $data['graphic'] as $singleMaterial ){
					$material[] = array_merge(
					$singleMaterial,
					array(
							'weixinSubscribeId'=>$weixinSubscribeId,
							'sort'=>$sort++
					)
					);
				}
		}elseif(strstr($_SERVER['HTTP_REFERER'], 'singleGraphic')){
			$data['weixinSubscribeId'] = $weixinSubscribeId;
			$data['sort'] = $sort;
			unset($data['graphic']);
			$material[]=$data;
		}elseif(strstr($_SERVER['HTTP_REFERER'], 'theText')){
			$data['weixinSubscribeId'] = $weixinSubscribeId;
			$data['sort'] = $sort;
			unset($data['graphic']);
			$material[]=$data;
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

	/*获取被关注列表*/
	public function getMysubscribe($userId){
		//拉取素材资料，获取已发布信息。
		$data['userId']=$userId;
		$data['isRelease']=2;//已发布
		return $this->wxSubscribeDb->search($data,array())['data'][0];
	}
	}
?>