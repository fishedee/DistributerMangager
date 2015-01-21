<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserAo extends CI_Model {
	
	var $TYPE_ADMIN = 0;
	var $TYPE_USER = 1;
	public function __construct(){
		parent::__construct();
		$this->load->model('user/userDb','userDb');
		$this->load->model('user/userClientDb','userClientDb');
		$this->load->model('user/userPermissionDb','userPermissionDb');
	}
	
	public function search($dataWhere,$dataLimit){
		return $this->userDb->search($dataWhere,$dataLimit);
	}
	
	public function get($userId){
		$result = $this->userDb->get($userId);
		if( $result['code'] != 0 )
			return $result;
		$user = $result['data'];
		
		$result = $this->userPermissionDb->getByUser($userId);
		if($result['code'] != 0 )
			return $result;
		$user['permission'] = __::pluck($result['data'],'permissionId');
		
		$result = $this->userClientDb->getByUser($userId);
		if($result['code'] != 0 )
			return $result;
		$userIds = __::pluck($result['data'],'clientUserId');
		
		$result = $this->userDb->getByIds($userIds);
		if($result['code'] != 0 )
			return $result;
		$user['client'] = $result['data'];  
		
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$user
		);
	}
	
	public function del($userId){
		$result = $this->userDb->del($userId);
		if( $result['code'] != 0 )
			return $result;
			
		$result = $this->userPermissionDb->delByUser($userId);
		if($result['code'] != 0 )
			return $result;
		
		$result = $this->userClientDb->delByUser($userId);
		if($result['code'] != 0 )
			return $result;
		
		return $result;
	}
	
	public function add($data){
		//检查是否有重名
		$result = $this->userDb->getByName($data['name']);
		if( $result['code'] != 0 )
			return $result;
		$user = $result['data'];
		if( count($user) != 0 )
			return array(
				'code'=>1,
				'msg'=>'存在重复的用户名',
				'data'=>''
			);
		
		//添加用户基本信息
		$userBaseInfo = $data;
		unset($userBaseInfo['permission']);
		unset($userBaseInfo['client']);
		$userBaseInfo['password'] = sha1($userBaseInfo['password']);
		$result = $this->userDb->add($userBaseInfo);
		if( $result['code'] != 0 )
			return $result;
		$userId = $result['data'];
		
		//添加用户权限
		$userPermissionInfo = array();
		foreach( $data['permission'] as $single ){
			$userPermissionInfo[] = array(
				'userId'=>$userId,
				'permissionId'=>$single
			);
		};
		$result = $this->userPermissionDb->addBatch($userPermissionInfo);
		if( $result['code'] != 0 )
			return $result;
		
		//添加用户客户列表
		$userClientInfo = array();
		foreach( $data['client'] as $single ){
			$userClientInfo[] = array(
				'userId'=>$userId,
				'clientUserId'=>$single['userId']
			);
		};
		$result = $this->userClientDb->addBatch($userClientInfo);
		if( $result['code'] != 0 )
			return $result;
			
		return $result;
	}
	
	public function mod($userId,$data){
		//修改用户基本信息
		$userBaseInfo = $data;
		unset($userBaseInfo['permission']);
		unset($userBaseInfo['client']);
		$result = $this->userDb->mod($userId,$userBaseInfo);
		if( $result['code'] != 0 )
			return $result;
			
		//修改用户权限
		$userPermissionInfo = array();
		foreach( $data['permission'] as $single ){
			$userPermissionInfo[] = array(
				'userId'=>$userId,
				'permissionId'=>$single
			);
		};
		$result = $this->userPermissionDb->delByUser($userId);
		if($result['code'] != 0 )
			return $result;
		
		$result = $this->userPermissionDb->addBatch($userPermissionInfo);
		if( $result['code'] != 0 )
			return $result;
			
		//修改用户客户列表
		$userClientInfo = array();
		foreach( $data['client'] as $single ){
			$userClientInfo[] = array(
				'userId'=>$userId,
				'clientUserId'=>$single['userId']
			);
		};
		$result = $this->userClientDb->delByUser($userId);
		if($result['code'] != 0 )
			return $result;
		
		$result = $this->userClientDb->addBatch($userClientInfo);
		if( $result['code'] != 0 )
			return $result;
			
		return $result;
	}
	
	public function modPassword($userId,$password){
		$data = array();
		$data['password'] = sha1($password);
		return $this->userDb->mod($userId,$data);
	}
	
	public function modPasswordByOld($userId,$oldPassword,$newPassword){
		//检查是否有重名
		$result = $this->userDb->getByIdAndPass($userId,sha1($oldPassword));
		if( $result['code'] != 0 )
			return $result;
		$users = $result['data'];
		if( count($users) == 0 )
			return array(
				'code'=>1,
				'msg'=>'原密码错误',
				'data'=>''
			);
		
		//修改密码
		$data = array();
		$data['password'] = sha1($newPassword);
		return $this->userDb->mod($userId,$data);
	}
}
