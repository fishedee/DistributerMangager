<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyClassifyAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('classify/companyClassifyDb','companyClassifyDb');
		$this->load->model('article/companyArticleWhen','companyArticleWhen');
	}
	
	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->companyClassifyDb->search($dataWhere,$dataLimit);
	}
	
	public function get($userCompanyClassifyId){
		$classify = $this->companyClassifyDb->get($userCompanyClassifyId);
		
		return $classify;
	}
	
	public function del($userId,$userCompanyClassifyId){
		$classify = $this->companyClassifyDb->get($userCompanyClassifyId);
		if($classify['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
			
		$this->companyClassifyDb->del($userCompanyClassifyId);
		
		//通知文章挂载的相关分类被删除了
		$this->companyArticleWhen->whenClassifyDelete($userCompanyClassifyId);
	}
	
	public function add($userId,$data){
		$maxSort = $this->companyClassifyDb->getMaxSortByUser($userId);
		$data['userId'] = $userId;
		if( $maxSort == null )
			$data['sort'] = 1;
		else
			$data['sort'] = $maxSort + 1;
		$data['userId'] = $userId;
		$this->companyClassifyDb->add($data);
	}
	
	public function mod($userId,$userCompanyClassifyId,$data){
		$classify = $this->companyClassifyDb->get($userCompanyClassifyId);
		if($classify['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
		
		$this->companyClassifyDb->mod($userCompanyClassifyId,$data);
	}
	
	public function move($userId,$userCompanyClassifyId,$direction){
		//取出所有的分类
		$dataWhere['userId'] = $userId;
		$allClassify = $this->companyClassifyDb->search($dataWhere,array());
		$allClassify = $allClassify['data'];
		
		//计算上一级的banner，与下一级的banner
		$index = -1;
		foreach( $allClassify as $key=>$singleClassify){
			if( $singleClassify['userCompanyClassifyId'] == $userCompanyClassifyId){
				$index = $key;
				break;
			}
		}
		if( $index === false )
			throw new CI_MyException(1,'不存在此分类');
		$currentClassify = $allClassify[$index];
		
		//调整sort值
		if( $direction == 'up' ){
			if( $index - 1 < 0 )
				throw new CI_MyException(1,'不能再往上调整了');
			$prevClassify =  $allClassify[$index - 1];
			$newCurrentSort = $prevClassify['sort'];
			$newCurrentId = $currentClassify['userCompanyClassifyId'];
			$newOtherSort = $currentClassify['sort'];
			$newOtherId = $prevClassify['userCompanyClassifyId'];
		}else{
			if( $index + 1 >= count($allClassify) )
				throw new CI_MyException(1,'不能再下调整了');
			$nextClassify =  $allClassify[$index + 1];
			$newCurrentSort = $nextClassify['sort'];
			$newCurrentId = $currentClassify['userCompanyClassifyId'];
			$newOtherSort = $currentClassify['sort'];
			$newOtherId = $nextClassify['userCompanyClassifyId'];
		}
		
		//更新数据库
		$this->companyClassifyDb->mod($newOtherId,array('sort'=>$newOtherSort));
		$this->companyClassifyDb->mod($newCurrentId,array('sort'=>$newCurrentSort));
	}
}
