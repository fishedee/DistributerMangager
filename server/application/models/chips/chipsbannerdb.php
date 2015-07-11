<?php 
/**
 * @author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsBannerDb extends CI_Model{
		private $tableName = 't_chips_banner';

		public function __construct(){
			parent::__construct();
		}

		//后台获取banner图
		public function getBannerBack($userId){
			$this->db->where('userId',$userId);
			$bannerInfo =  $this->db->get($this->tableName)->result_array();
			return array(
				'count' => count($bannerInfo),
				'data'  => $bannerInfo
				);
		}

		//前台获取banner图
		public function getBanner($userId){
			$condition['userId'] = $userId;
			$condition['status'] = 1;
			$bannerInfo = $this->db->where($condition)->get($this->tableName)->result_array();
			return $bannerInfo;
		}

		//编辑banner 获取详细信息
		public function getBannerDetailInfo($userId,$chips_banner_id){
			$bannerInfo = $this->db->where('chips_banner_id',$chips_banner_id)->get($this->tableName)->result_array();
			$bannerInfo = $bannerInfo[0];
			if($bannerInfo['userId'] != $userId){
				throw new CI_MyException(1,'非法操作');
			}else{
				return $bannerInfo;
			}
		}

		//更新banner图信息
		public function updateBanner($userId,$chips_banner_id,$data){
			$bannerInfo = $this->db->select('userId')->from($this->tableName)->where('chips_banner_id',$chips_banner_id)->get()->result_array();
			$bannerInfo = $bannerInfo[0];
			if($bannerInfo['userId'] != $userId){
				return false;
			}
			$this->db->where('chips_banner_id',$chips_banner_id);
			$this->db->update($this->tableName,$data);
			if($this->db->affected_rows() > 0){
				return true;
			}else{
				return false;
			}
		}

		//增加banner图
		public function add($data){
			$this->db->insert($this->tableName,$data);
			return $this->db->insert_id();
		}

		//banner图显示与否
		public function showOrHide($userId,$chips_banner_id){
			$bannerInfo = $this->db->select('userId,status')->from($this->tableName)->where('chips_banner_id',$chips_banner_id)->get()->result_array();
			$bannerInfo = $bannerInfo[0];
			if($bannerInfo['userId'] != $userId){
				return false;
			}
			if($bannerInfo['status'] == 0){
				$data['status'] = 1;
			}else{
				$data['status'] = 0;
			}
			$this->db->where('chips_banner_id',$chips_banner_id);
			$this->db->update($this->tableName,$data);
			if($this->db->affected_rows() > 0){
				return true;
			}else{
				return false;
			}
		}

		//删除banner图
		public function del($userId,$chips_banner_id){
			$bannerInfo = $this->db->select('userId')->from($this->tableName)->where('chips_banner_id',$chips_banner_id)->get()->result_array();
			$bannerInfo = $bannerInfo[0];
			if($bannerInfo['userId'] != $userId){
				return false;
			}
			$this->db->delete($this->tableName,array('chips_banner_id' => $chips_banner_id));
			if($this->db->affected_rows() > 0){
				return true;
			}else{
				return false;
			}
		}

	}
 ?>