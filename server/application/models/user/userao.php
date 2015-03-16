<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('user/userDb','userDb');
		$this->load->model('user/userClientDb','userClientDb');
		$this->load->model('user/userPermissionDb','userPermissionDb');
	}
	
	public function checkMustVaildPassword($password,$passwordHash){
		if( password_verify($password,$passwordHash) == false )
			throw new CI_MyException(1,'密码不正确');
	}

	public function getPasswordHash($password){
		return password_hash($password,PASSWORD_BCRYPT);
	}

	public function getByName($name){
		return $this->userDb->getByName($name);
	}


	public function search($dataWhere,$dataLimit){
		return $this->userDb->search($dataWhere,$dataLimit);
	}
	
	public function get($userId){
		$user = $this->userDb->get($userId);
		
		$userPermission = $this->userPermissionDb->getByUser($userId);
		$user['permission'] = array_map(function($single){
			return $single['permissionId'];
		},$userPermission);
		
		$userClient = $this->userClientDb->getByUser($userId);
		$userClientIds = array_map(function($single){
			return $single['clientUserId'];
		},$userClient);
		$user['client'] = $this->userDb->getByIds($userClientIds);
		$user['url'] = 'http://'.$userId.'.'.$_SERVER['HTTP_HOST'].'/'.$userId;
		return $user;
	}
	
	public function del($userId){
		$this->userDb->del($userId);
			
		$this->userPermissionDb->delByUser($userId);
		
		$this->userClientDb->delByUser($userId);
	}
	
	public function add($data){
		//检查是否有重名
		$user = $this->userDb->getByName($data['name']);
		if( count($user) != 0 )
			throw new CI_MyException(1,'存在重复的用户名');
		
		//添加用户基本信息
		$userBaseInfo = $data;
		unset($userBaseInfo['permission']);
		unset($userBaseInfo['client']);
		$userBaseInfo['password'] = $this->getPasswordHash($userBaseInfo['password']);
		$userId = $this->userDb->add($userBaseInfo);
		
		//添加用户权限
		if( isset($data['permission']) ){
			$userPermissionInfo = array_map(function($single)use($userId){
				return array(
					'userId'=>$userId,
					'permissionId'=>$single
				);
			},$data['permission']);
			$this->userPermissionDb->addBatch($userPermissionInfo);
		}
		
		//添加用户客户列表
		if( isset($data['client']) ){
			$userClientInfo = array_map(function($single)use($userId){
				return array(
					'userId'=>$userId,
					'clientUserId'=>$single['userId']
				);
			},$data['client']);
			$this->userClientDb->addBatch($userClientInfo);
		}
	}
	
	public function mod($userId,$data){
		//修改用户基本信息
		$userBaseInfo = $data;
		unset($userBaseInfo['permission']);
		unset($userBaseInfo['client']);
		$this->userDb->mod($userId,$userBaseInfo);
			
		//修改用户权限
		if( isset($data['permission']) ){
			$userPermissionInfo = array_map(function($single)use($userId){
				return array(
					'userId'=>$userId,
					'permissionId'=>$single
				);
			},$data['permission']);
			$this->userPermissionDb->delByUser($userId);
			$this->userPermissionDb->addBatch($userPermissionInfo);
		}
			
		//修改用户客户列表
		if( isset($data['client']) ){
			$userClientInfo = array_map(function($single)use($userId){
				return array(
					'userId'=>$userId,
					'clientUserId'=>$single['userId']
				);
			},$data['client']);
			$this->userClientDb->delByUser($userId);
			$this->userClientDb->addBatch($userClientInfo);
		}
	}
	
	public function modPassword($userId,$password){
		$data = array();
		$data['password'] = $this->getPasswordHash($password);
		$this->userDb->mod($userId,$data);
	}
	
	public function modPasswordByOld($userId,$oldPassword,$newPassword){
		//检查是否有重名
		$user = $this->userDb->get($userId);
		$this->checkMustVaildPassword($oldPassword,$user['password']);
		
		//修改密码
		$this->modPassword($userId,$this->getPasswordHash($newPassword));
	}
}
