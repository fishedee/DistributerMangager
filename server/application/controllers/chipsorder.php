<?php 
/**
 * author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsOrder extends CI_Controller{
		private $userId;
		private $clientId;

		public function __construct(){
			parent::__construct();
			//加载各类需要的模型
			$this->load->model('user/user_model','usermodel');
			$this->load->model('chips/chipsAo','chipsAo');
			$this->load->model('chips/chipsOrderAo','chipsOrderAo');
			$this->load->model('address/address_model','address');
			$this->load->model('chips/chipsOrderPayDb','pay');
			$this->load->library('argv', 'argv');
			$clientId = $this->session->userdata('clientId');
			$this->clientId = $clientId;
		}

		/**
		 * @view json
		 */
		public function down(){
			if($this->input->is_ajax_request()){
				$argv = $this->argv->check(array(
		            array('userId', 'require')
		        ));
		        $userId   = $argv['userId'];
		        $chips_id = $this->input->post('chips_id');
				$num      = $this->input->post('num');
				// 更新收货地址
				$name = $this->input->post('name');
				$province = $this->input->post('province');
				$city = $this->input->post('city');
				$address = $this->input->post('address');
				$phone = $this->input->post('phone');
				$addressId = $this->address->upOrAdd($this->clientId,$name,$province,$city,$address,$phone);
				//插入我的订单
				$result = $this->chipsOrderAo->down($this->clientId,$userId,$chips_id,$num,$addressId,$name,$province,$city,$address,$phone);
				return $result;
			}
		}

		/**
		 * @view json
		 */
		public function payFirst(){
			if($this->input->is_ajax_request()){
				//检查输入参数
				$data = $this->argv->checkGet(array(
					array('userId', 'require'),
				));
				$userId = $data['userId'];
				$chips_order_id = $this->input->post('chips_order_id');
				return $this->chipsOrderAo->wxJsPay($userId,$chips_order_id);
			}
		}

		/**
		 * @view json
		 * 前端获取订单列表
		 */
		public function getOrderList(){
			if($this->input->is_ajax_request()){
				$state = $_GET['state'];
				$orderInfo = $this->chipsOrderAo->getOrderList($state,$this->clientId);
				return $orderInfo;
			}
		}

		/**
		 * @view json
		 */
		public function search(){
			if($this->input->is_ajax_request()){
				if($this->session->userdata('userId')){
					$result = $this->usermodel->checkPermission($this->session->userdata('userId'));
					if(!$result){
						throw new CI_MyException(1,'您没有权限');
					}
				}
				$dataWhere = $this->argv->checkGet(array(
					array('type','option'),
				));
				$userId    = $this->session->userdata('userId');
		        $dataLimit = $this->argv->checkGet(array(
					array('pageIndex','require'),
					array('pageSize','require'),
				));
				$dataWhere['userId'] = $userId;
				$orderInfo_data =  $this->chipsOrderAo->search($dataWhere,$dataLimit);
				$orderInfo = $orderInfo_data['data'];
				foreach ($orderInfo as $key => $value) {
					if($value['status'] == 1){
						$show_status = '用户下单，未预付';
					}elseif($value['status'] == 2){
						$show_status = '用户已预付';
					}elseif($value['status'] == 3){
						$show_status = '用户已全额支付';
					}
					$orderInfo[$key]['show_status'] = $show_status;
				}
				$orderInfo_data['data'] = $orderInfo;
				return $orderInfo_data;
			}
		}

		/**
		* @view json
		*/
		public function wxpaycallback($userId=10007){
			//业务逻辑
			$data = $this->pay->wxPayCallback($userId);

			log_message('info','wxpaycallback:'.json_encode($data));
		}

		/**
		 * @view json
		 */
		public function payAll(){
			if($this->input->is_ajax_request()){
				//检查输入参数
				$data = $this->argv->checkGet(array(
					array('userId', 'require'),
				));
				$userId = $data['userId'];
				$chips_id = $this->input->post('chips_id');
				$chips_order_id = $this->input->post('chips_order_id');
				return $this->chipsOrderAo->payAll($userId,$this->clientId,$chips_id,$chips_order_id);
			}
		}

		/**
		 * @view json
		 */
		public function orderFirstPay(){
			if($this->input->is_ajax_request()){
				//检查输入参数
				$data = $this->argv->checkGet(array(
					array('userId', 'require'),
				));
				$userId = $data['userId'];
				$chips_order_id = $this->input->post('chips_order_id');
				return $this->chipsOrderAo->orderFirstPay($userId,$chips_order_id);
			}
		}

		//全额支付完成
		public function payAllAfter(){
			if($this->input->is_ajax_request()){
				$chips_order_id = $this->input->post('chips_order_id');
				$result = $this->chipsOrderAo->payAllAfter($chips_order_id);
				echo json_encode($result);
			}
		}

		/**
		 * @view json
		 */
		public function getOrderCount(){
			if($this->input->is_ajax_request()){
				//检查输入参数
				$data = $this->argv->checkGet(array(
					array('userId', 'require'),
				));
				$userId = $data['userId'];
				$clientId = $this->clientId;
				return $this->chipsOrderAo->getOrderCount($userId,$clientId);
			}
		}

		/**
		 * @view json
		 */
		public function rePayFirst(){
			if($this->input->is_ajax_request()){
				//检查输入参数
				$data = $this->argv->checkGet(array(
					array('userId', 'require'),
				));
				$userId = $data['userId'];
				$chips_order_id = $this->input->post('chips_order_id');
				return $this->chipsOrderAo->rePayFirst($userId,$chips_order_id);
			}
		}

		/**
		 * @view json
		 */
		public function rePayAll(){
			if($this->input->is_ajax_request()){
				//检查输入参数
				$data = $this->argv->checkGet(array(
					array('userId', 'require'),
				));
				$userId = $data['userId'];
				$chips_order_id = $this->input->post('chips_order_id');
				$chips_id = $this->input->post('chips_id');
				return $this->chipsOrderAo->rePayAll($userId,$chips_order_id,$chips_id);
			}
		}
	}
 ?>