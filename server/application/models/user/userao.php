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
		if( isset($dataWhere['permissionId'])){
			$users = $this->userPermissionDb->search(
				array('permissionId'=>$dataWhere['permissionId']),
				array()
			);
			if( $users['count'] == 0 )
				return array('count'=>0,'data'=>array());
			$userIds = array_map(function($single){
				return $single['userId'];
			},$users['data']);
			$dataWhere['userId'] = $userIds;
		}
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
		throw new CI_MyException(1,'禁止删掉用户，删掉用户非常容易导致严重的数据不一致问题，你可以禁止他的权限来屏蔽他的使用');

		$this->userDb->del($userId);
			
		$this->userPermissionDb->delByUser($userId);
		
		$this->userClientDb->delByUser($userId);
	}

	private function check($data){
		if( isset($data['name'])){
			$data['name'] = trim($data['name']);
			if( preg_match('/^[0-9A-Za-z]+$/',$data['name']) == 0 )
				throw new CI_MyException(1,'请输入数字或英文字母组成的名字');
		}

		if( isset($data['password'])){
			$data['password'] = trim($data['password']);
			if( $data['password'] == '')
				throw new CI_MyException(1,'请输入密码');
		}

		if(isset($data['password']) && isset($data['password2'])){
			$data['password'] = trim($data['password']);
			$data['password2']= trim($data['password2']);
			if($data['password'] != $data['password2'])
				throw new CI_MyException(1,'两次密码不一致');
			unset($data['password2']);
		}

		if( isset($data['company'])){
			$data['company'] = trim($data['company']);
			if( $data['company'] == '')
				throw new CI_MyException(1,'请输入公司名称');
		}
		
		if( isset($data['phone'])){
			$data['phone'] = trim($data['phone']);
			if( preg_match('/^[0-9]{11}$/',$data['phone']) == 0 )
				throw new CI_MyException(1,'请输入11位的联系人手机号码');
		}

		if( isset($data['telephone'])){
			$data['telephone'] = trim($data['telephone']);
			if( preg_match('/^[0-9-]+$/',$data['telephone']) == 0 )
				throw new CI_MyException(1,'请输入只包含数字的电话号码');
		}

		if( isset($data['email'])){
			$data['email'] = trim($data['email']);
			if( preg_match('/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i',$data['email']) == 0)
				throw new CI_MyException(1,'输入的电子邮箱非法');
			$result = $this->userDb->checkEmail($data['email']);
			if($result)
				throw new CI_MyException(1,'该邮箱已经被占用');
		}

		return $data;
	}
	
	public function add($data){
		//校验数据
		$data = $this->check($data);

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

		return $userId;
	}
	
	public function mod($userId,$data){
		//校验数据
		$data = $this->check($data);
		
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
		$this->modPassword($userId,$newPassword);
	}

	/**
	 * @author:zzh
	 * 2015.8.5
	 */

	public function searchOpenId($openId){
		return $this->userDb->searchOpenId($openId);
	}

	//绑定userId 和 openId
	public function bind($userId,$openId){
		return $this->userDb->bind($userId,$openId);
	}

	//解绑
	public function unBind($userId){
		return $this->userDb->unBind($userId);
	}

	//检测登录名和密码
	public function checkLoginInfo($username,$hasPassword,$password){
		return $this->userDb->checkLoginInfo($username,$hasPassword,$password);
	}
}
