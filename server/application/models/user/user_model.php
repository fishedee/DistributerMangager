<?php 
/**
 * author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class User_model extends CI_Model{
		private $tableName = 't_user';

		public function __construct(){
			parent::__construct();
		}

		//检测用户是否登陆 防止客户端信息被篡改
		public function checkUserId($userId){
			$userIdInfo = $this->db->select('userId',$userId)->from($this->tableName)->where('userId',$userId)->get()->result_array();
			return $userIdInfo;
		}

		//用户信息
		public function checkUserInfo($userId){
			$userInfo = $this->db->where('userId',$userId)->from($this->tableName)->get()->result_array();
			return $userInfo;
		}

		//用户类型
		public function getUserType($userId){
			$userTypeInfo = $this->db->select('type')->from($this->tableName)->where('userId',$userId)->get()->result_array();
			return $userTypeInfo[0]['type'];
		}

		//获取app信息
		public function getAppInfo($userId){
			$this->db->where('userId',$userId);
			$userInfo = $this->db->get('t_user_app')->result_array();
			return $userInfo[0];
		}

		//检测permission
		public function checkPermission($userId){
			$condition['userId'] = $userId;
			$condition['permissionId'] = 6;
			$result = $this->db->where($condition)->get('t_user_permission')->result_array();
			return $result;
		}
	}
 ?>