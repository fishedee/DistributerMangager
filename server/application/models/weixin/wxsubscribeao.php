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
	public function del($userId,$weixinSubscribeId,$data=null){
		$this->wxSubscribeDb->del($userId,$weixinSubscribeId);
		$this->wxMaterialDb->del($weixinSubscribeId,$data);//第二参数，更新文件状态
	}

	/*获取被关注列表*/
	public function getMysubscribe($userId){
		//拉取素材资料，获取已发布信息。
		$data['userId']=$userId;
		$data['isRelease']=2;//已发布
	    $data2=$this->wxSubscribeDb->search($data,array())['data'][0];
        if($data2 == null){
                 return '';
        }else {
                return $data2;
        }
		
	}

	/*获取素材ID信息*/
	public function getKeyResponseId($userId){
		return $this->wxSubscribeDb->getKeyResponseId($userId);
	}

	/*增加关键字的回复*/
	public function addKey($data){
		foreach ($data as $key => $value) {
			if(!$value){
				throw new CI_MyException(1,'参数不齐');
			}
		}
		return $this->wxSubscribeDb->addKey($data);
	}

	/*更新*/
	public function updateKey($keyResponseId,$data){
		foreach ($data as $key => $value) {
			if(!$value){
				throw new CI_MyException(1,'参数不齐');
			}
		}
		return $this->wxSubscribeDb->updateKey($keyResponseId,$data);
	}

	/*关键词自动回复*/
	public function keySearch($dataWhere,$dataLimit){
		return $this->wxSubscribeDb->keySearch($dataWhere,$dataLimit);
	}

	/*删除关键词自动回复*/
	public function keyResponseDel($keyResponseId){
		return $this->wxSubscribeDb->keyResponseDel($keyResponseId);
	}

	/*获取详细信息*/
	public function getKeyResponseInfo($keyResponseId){
		return $this->wxSubscribeDb->getKeyResponseInfo($keyResponseId);
	}

	/*根据关键字查询*/
	public function keyWordSearch($keyword){
		$weixinSubscribeId =  $this->wxSubscribeDb->keyWordSearch($keyword);
		//根据微信素材id查询素材类型
		$materialClassifyId = $this->wxSubscribeDb->materialClassifyIdSearch($weixinSubscribeId);
		return array(
			'weixinSubscribeId' => $weixinSubscribeId,
			'materialClassifyId' => $materialClassifyId
			);
	}

	/*根据素材id 查询素材的内容*/
	public function materialSearch($weixinSubscribeId){
		$materialInfo = $this->wxMaterialDb->getByWeixinSubscribeId($weixinSubscribeId);
		return $materialInfo;
	}
}
?>