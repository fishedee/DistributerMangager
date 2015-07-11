<?php 
/**
 * @author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsBanner extends CI_Controller{
		private $userId;

		public function __construct(){
			parent::__construct();
			//加载各类需要的模型
			$this->load->model('user/user_model','usermodel');
			$this->load->model('chips/chipsBannerDb','banner');
			$this->load->library('argv', 'argv');
			$userId = $this->session->userdata('userId');
			$this->userId = $userId;
			if(!$userId){
				echo '非法操作';die;
			}
			$userIdInfo = $this->usermodel->checkUserId($userId);
			if(empty($userIdInfo)){
				echo '非法操作';die;
			}
		}
		/**
		 * @view json
		 */
		public function getBannerBack(){
			if($this->input->is_ajax_request()){
				if($this->session->userdata('userId')){
					$result = $this->usermodel->checkPermission($this->session->userdata('userId'));
					if(!$result){
						throw new CI_MyException(1,'您没有权限');
					}
				}
				$bannerInfo_show = $this->banner->getBannerBack($this->userId);
				$bannerInfo = $bannerInfo_show['data'];
				foreach ($bannerInfo as $key => $value) {
					if($value['status'] == 1){
						$bannerInfo[$key]['show_status'] = '显示';
					}else{
						$bannerInfo[$key]['show_status'] = '隐藏';
					}
				}
				$bannerInfo_show['data'] = $bannerInfo;
				return $bannerInfo_show;
			}
		}

		//前台获取banner
		public function getBanner(){
			if($this->input->is_ajax_request()){
				$argv = $this->argv->check(array(
		            array('userId', 'require')
		        ));
		        $userId = $argv['userId'];
		        $result = $this->banner->getBanner($userId);
		        echo json_encode($result);
			}
		}

		/**
		 * @view json
		 */
		public function bannerDetailInfo(){
			if($this->input->is_ajax_request()){
				if($this->session->userdata('userId')){
					$result = $this->usermodel->checkPermission($this->session->userdata('userId'));
					if(!$result){
						throw new CI_MyException(1,'您没有权限');
					}
				}
				$chips_banner_id = $this->input->post('chips_banner_id');
				return $this->banner->getBannerDetailInfo($this->userId,$chips_banner_id);
			}
		}

		//更新banner图
		public function updateChipsBanner(){
			if($this->input->is_ajax_request()){
				if($this->session->userdata('userId')){
					$result = $this->usermodel->checkPermission($this->session->userdata('userId'));
					if(!$result){
						echo ('您没有权限');die;
					}
				}
				$chips_banner_id = $this->input->post('chips_banner_id');
				$data = $this->input->post('data');
				$result = $this->banner->updateBanner($this->userId,$chips_banner_id,$data);
				if($result == true){
					echo 1;
				}else{
					echo 0;
				}
			}
		}

		//增加banner图
		public function add(){
			if($this->input->is_ajax_request()){
				if($this->session->userdata('userId')){
					$result = $this->usermodel->checkPermission($this->session->userdata('userId'));
					if(!$result){
						echo ('您没有权限');die;
					}
				}
				$data = $this->input->post('data');
				$data['userId'] = $this->userId;
				$result = $this->banner->add($data);
				if($result){
					echo 1;
				}else{
					echo 0;
				}
			}
		}

		//更改banner图状态
		public function showOrHide(){
			if($this->input->is_ajax_request()){
				if($this->session->userdata('userId')){
					$result = $this->usermodel->checkPermission($this->session->userdata('userId'));
					if(!$result){
						echo ('您没有权限');die;
					}
				}
				$chips_banner_id = $this->input->post('chips_banner_id');
				$result = $this->banner->showOrHide($this->userId,$chips_banner_id);
				if($result == true){
					echo 1;
				}else{
					echo 0;
				}
			}
		}

		//删除banner图
		public function del(){
			if($this->input->is_ajax_request()){
				if($this->session->userdata('userId')){
					$result = $this->usermodel->checkPermission($this->session->userdata('userId'));
					if(!$result){
						echo ('您没有权限');die;
					}
				}
				$chips_banner_id = $this->input->post('chips_banner_id');
				$result = $this->banner->del($this->userId,$chips_banner_id);
				if($result == true){
					echo 1;
				}else{
					echo 0;
				}
			}
		}
	}
 ?>