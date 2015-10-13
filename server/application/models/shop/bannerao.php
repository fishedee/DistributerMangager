<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BannerAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('shop/bannerDb','bannerDb');
	}
	
	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->bannerDb->search($dataWhere,$dataLimit);
	}
	
	public function get($userId,$userShopBannerId){
		$article = $this->bannerDb->get($userShopBannerId);
		if($article['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
		
		return $article;
	}
	
	public function del($userId,$userShopBannerId){
		$article = $this->bannerDb->get($userShopBannerId);
		if($article['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
		
		@unlink(dirname(__FILE__).'/../../../..'.$article['icon']);
		$this->bannerDb->del($userShopBannerId);
	}
	
	public function add($userId,$data){

		$maxSort = $this->bannerDb->getMaxSortByUser($userId);
		$data['userId'] = $userId;
		if( $maxSort == null )
			$data['sort'] = 1;
		else
			$data['sort'] = $maxSort + 1;
		$this->bannerDb->add($data);
	}
	
	public function mod($userId,$userShopBannerId,$data){
		$article = $this->bannerDb->get($userShopBannerId);
		if($article['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
		
		$this->bannerDb->mod($userShopBannerId,$data);
	}
	
	public function move($userId,$userShopBannerId,$direction){
		//取出所有的广告
		$dataWhere['userId'] = $userId;
		$allBanner = $this->bannerDb->search($dataWhere,array());
		$allBanner = $allBanner['data'];
		
		//计算上一级的banner，与下一级的banner
		$index = -1;
		foreach( $allBanner as $key=>$singleBanner){
			if( $singleBanner['userShopBannerId'] == $userShopBannerId){
				$index = $key;
				break;
			}
		}
		if( $index == -1 )
			throw new CI_MyException(1,'不存在此广告');
		$currentBanner = $allBanner[$index];
		
		//调整sort值
		if( $direction == 'up' ){
			if( $index - 1 < 0 )
				throw new CI_MyException(1,'不能再往上调整了');
			$prevBanner =  $allBanner[$index - 1];
			$newCurrentSort = $prevBanner['sort'];
			$newCurrentId = $currentBanner['userShopBannerId'];
			$newOtherSort = $currentBanner['sort'];
			$newOtherId = $prevBanner['userShopBannerId'];
		}else{
			if( $index + 1 >= count($allBanner) )
				throw new CI_MyException(1,'不能再下调整了');
			$nextBanner =  $allBanner[$index + 1];
			$newCurrentSort = $nextBanner['sort'];
			$newCurrentId = $currentBanner['userShopBannerId'];
			$newOtherSort = $currentBanner['sort'];
			$newOtherId = $nextBanner['userShopBannerId'];
		}
		
		//更新数据库
		$this->bannerDb->mod($newOtherId,array('sort'=>$newOtherSort));
		$this->bannerDb->mod($newCurrentId,array('sort'=>$newCurrentSort));
	}
}
