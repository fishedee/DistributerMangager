<?php 
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class MemberCard extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('member/memberCardAo','memberCardAo');
			$this->load->library('argv', 'argv');
		}

		/**
		 * @view json
		 */
		public function add(){
			if($this->input->is_ajax_request()){
				$data = $this->input->post('data');
				$userId = $this->session->userdata('userId');
				return $this->memberCardAo->create($userId,$data);
			}
		}

		/**
		 * @view json
		 * 获取会员卡
		 */
		public function getMemberCard(){
			if($this->input->is_ajax_request()){
				$userId = $this->session->userdata('userId');
				$dataLimit = $this->argv->checkGet(array(
					array('pageIndex','require'),
					array('pageSize','require'),
				));
				//检查输入参数		
				$dataWhere = $this->argv->checkGet(array(
					array('title','option'),
				));
				return $this->memberCardAo->getMemberCard($userId,$dataLimit,$dataWhere);
			}
		}

		/**
		 * @view json
		 * 更新会员卡信息
		 */
		public function updateMemberCard(){
			if($this->input->is_ajax_request()){
				$userId = $this->session->userdata('userId');
				return $this->memberCardAo->updateMemberCard($userId);
			}
		}

		/**
		 * @view json
		 * 设置默认会员卡
		 */
		public function defaultCard(){
			if($this->input->is_ajax_request()){
				$userId = $this->session->userdata('userId');
				$card_id= $this->input->post('card_id');
				return $this->memberCardAo->defaultCard($userId,$card_id);
			}
		}

		/**
		 * @view json
		 */
		public function memberCardDetailInfo(){
			if($this->input->is_ajax_request()){
				$userId = $this->session->userdata('userId');
				$card_id= $this->input->post('card_id');
				return $this->memberCardAo->memberCardDetailInfo($userId,$card_id);
			}
		}

		/**
		 * @view json
		 * api方式更新会员卡
		 */
		public function apiUpdateMemberCard(){
			if($this->input->is_ajax_request()){
				$data = $this->input->post('data');
				$userId = $this->session->userdata('userId');
				$data['card_id'] = $this->input->post('card_id');
				return $this->memberCardAo->apiUpdateMemberCard($userId,$data);
			}
		}

		//获取会员卡信息
		public function getMemberCardInfo(){
			$userId = $this->session->userdata('userId');
			return $this->memberCardAo->getMemberCardInfo($userId);
		}

		//设置测试会员卡白名单
		public function white(){
			$userId = $this->session->userdata('userId');
			return $this->memberCardAo->white($userId);
		}
	}
 ?>