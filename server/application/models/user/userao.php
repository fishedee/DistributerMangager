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
		$user = $this->userDb->get($userId);
		
		$userPermission = $this->userPermissionDb->getByUser($userId);
		$user['permission'] = __::pluck($userPermission,'permissionId');
		
		$userClient = $this->userClientDb->getByUser($userId);
		$userClientIds = __::pluck($userClient,'clientUserId');
		$user['client'] = $this->userDb->getByIds($userClientIds);
		
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
		$userBaseInfo['password'] = sha1($userBaseInfo['password']);
		$userId = $this->userDb->add($userBaseInfo);
		
		//添加用户权限
		if( isset($data['permission']) ){
			$userPermissionInfo = __::map($data['permission'],function($single)use($userId){
				return array(
					'userId'=>$userId,
					'permissionId'=>$single
				);
			});
			$this->userPermissionDb->addBatch($userPermissionInfo);
		}
		
		//添加用户客户列表
		if( isset($data['client']) ){
			$userClientInfo = __::map($data['client'],function($single)use($userId){
				return array(
					'userId'=>$userId,
					'clientUserId'=>$single['userId']
				);
			});
			$this->userClientDb->addBatch($userClientInfo);
		}
			
		return $result;
	}
	
	public function mod($userId,$data){
		//修改用户基本信息
		$userBaseInfo = $data;
		unset($userBaseInfo['permission']);
		unset($userBaseInfo['client']);
		$this->userDb->mod($userId,$userBaseInfo);
			
		//修改用户权限
		if( isset($data['permission']) ){
			$userPermissionInfo = __::map($data['permission'],function($single)use($userId){
				return array(
					'userId'=>$userId,
					'permissionId'=>$single
				);
			});
			$this->userPermissionDb->delByUser($userId);
			$this->userPermissionDb->addBatch($userPermissionInfo);
		}
			
		//修改用户客户列表
		if( isset($data['client']) ){
			$userClientInfo = __::map($data['client'],function($single)use($userId){
				return array(
					'userId'=>$userId,
					'clientUserId'=>$single['userId']
				);
			});
			$this->userClientDb->delByUser($userId);
			$this->userClientDb->addBatch($userClientInfo);
		}
	}
	
	public function modPassword($userId,$password){
		$data = array();
		$data['password'] = sha1($password);
		$this->userDb->mod($userId,$data);
	}
	
	public function modPasswordByOld($userId,$oldPassword,$newPassword){
		//检查是否有重名
		$user = $this->userDb->getByIdAndPass($userId,sha1($oldPassword));
		if( count($user) == 0 )
			throw new CI_MyException(1,'原密码错误');
		
		//修改密码
		$this->modPassword($userId,$newPassword);
	}
}
