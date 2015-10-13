<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyArticleAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('article/companyArticleDb','companyArticleDb');
	}
	
	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->companyArticleDb->search($dataWhere,$dataLimit);
	}
	
	public function get($userCompanyArticleId){
		$article = $this->companyArticleDb->get($userCompanyArticleId);
		return $article;
	}
	
	public function del($userId,$userCompanyArticleId){
		$article = $this->companyArticleDb->get($userCompanyArticleId);
		if($article['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
			
		$this->companyArticleDb->del($userCompanyArticleId);
	}
	
	public function add($userId,$data){
		$maxSort = $this->companyArticleDb->getMaxSortByUser($userId);
		$data['userId'] = $userId;
		if( $maxSort == null )
			$data['sort'] = 1;
		else
			$data['sort'] = $maxSort + 1;
		$this->companyArticleDb->add($data);
	}
	
	public function mod($userId,$userCompanyArticleId,$data){
		$article = $this->companyArticleDb->get($userCompanyArticleId);
		if($article['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
		
		$this->companyArticleDb->mod($userCompanyArticleId,$data);
	}

	public function move($userId,$userCompanyArticleId,$direction){
		//取出所有的分类
		$dataWhere['userId'] = $userId;
		$allClassify = $this->companyArticleDb->search($dataWhere,array());
		$allClassify = $allClassify['data'];
		
		//计算上一级的banner，与下一级的banner
		$index = -1;
		foreach( $allClassify as $key=>$singleClassify){
			if( $singleClassify['userCompanyArticleId'] == $userCompanyArticleId){
				$index = $key;
				break;
			}
		}
		if( $index == -1 )
			throw new CI_MyException(1,'不存在此文章');
		$currentClassify = $allClassify[$index];
		
		//调整sort值
		if( $direction == 'up' ){
			if( $index - 1 < 0 )
				throw new CI_MyException(1,'不能再往上调整了');
			$prevClassify =  $allClassify[$index - 1];
			$newCurrentSort = $prevClassify['sort'];
			$newCurrentId = $currentClassify['userCompanyArticleId'];
			$newOtherSort = $currentClassify['sort'];
			$newOtherId = $prevClassify['userCompanyArticleId'];
		}else{
			if( $index + 1 >= count($allClassify) )
				throw new CI_MyException(1,'不能再下调整了');
			$nextClassify =  $allClassify[$index + 1];
			$newCurrentSort = $nextClassify['sort'];
			$newCurrentId = $currentClassify['userCompanyArticleId'];
			$newOtherSort = $currentClassify['sort'];
			$newOtherId = $nextClassify['userCompanyArticleId'];
		}
		
		//更新数据库
		$this->companyArticleDb->mod($newOtherId,array('sort'=>$newOtherSort));
		$this->companyArticleDb->mod($newCurrentId,array('sort'=>$newCurrentSort));
	}
}
