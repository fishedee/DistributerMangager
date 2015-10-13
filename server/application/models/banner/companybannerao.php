<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyBannerAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('banner/companyBannerDb','companyBannerDb');
	}
	
	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->companyBannerDb->search($dataWhere,$dataLimit);
	}
	
	public function get($userId,$userCompanyBannerId){
		$article = $this->companyBannerDb->get($userCompanyBannerId);
		if($article['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
		
		return $article;
	}
	
	public function del($userId,$userCompanyBannerId){
		$article = $this->companyBannerDb->get($userCompanyBannerId);
		if($article['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
			
		$this->companyBannerDb->del($userCompanyBannerId);
	}
	
	public function add($userId,$data){

		$maxSort = $this->companyBannerDb->getMaxSortByUser($userId);
		$data['userId'] = $userId;
		if( $maxSort == null )
			$data['sort'] = 1;
		else
			$data['sort'] = $maxSort + 1;
		$this->companyBannerDb->add($data);
	}
	
	public function mod($userId,$userCompanyBannerId,$data){
		$article = $this->companyBannerDb->get($userCompanyBannerId);
		if($article['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
		
		$this->companyBannerDb->mod($userCompanyBannerId,$data);
	}
	
	public function move($userId,$userCompanyBannerId,$direction){
		//取出所有的广告
		$dataWhere['userId'] = $userId;
		$allBanner = $this->companyBannerDb->search($dataWhere,array());
		$allBanner = $allBanner['data'];
		
		//计算上一级的banner，与下一级的banner
		$index = -1;
		foreach( $allBanner as $key=>$singleBanner){
			if( $singleBanner['userCompanyBannerId'] == $userCompanyBannerId){
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
			$newCurrentId = $currentBanner['userCompanyBannerId'];
			$newOtherSort = $currentBanner['sort'];
			$newOtherId = $prevBanner['userCompanyBannerId'];
		}else{
			if( $index + 1 >= count($allBanner) )
				throw new CI_MyException(1,'不能再下调整了');
			$nextBanner =  $allBanner[$index + 1];
			$newCurrentSort = $nextBanner['sort'];
			$newCurrentId = $currentBanner['userCompanyBannerId'];
			$newOtherSort = $currentBanner['sort'];
			$newOtherId = $nextBanner['userCompanyBannerId'];
		}
		
		//更新数据库
		$this->companyBannerDb->mod($newOtherId,array('sort'=>$newOtherSort));
		$this->companyBannerDb->mod($newCurrentId,array('sort'=>$newCurrentSort));
	}
}
