<?php 
/**
 * @author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsOrderAo extends CI_Model{
		public function __construct(){
			parent::__construct();
			$this->load->model('chips/chipsAo','chipsAo');
			$this->load->model('chips/chipsOrderPayDb','pay');
			$this->load->model('chips/chipsRecordDb','record');
			$this->load->model('chips/chipsOrderDb','chipsOrderDb');
		}

		//后台查看订单
		public function search($dataWhere,$limit){
			return $this->chipsOrderDb->search($dataWhere,$limit);
		}

		//前端获取订单列表
		public function getOrderList($state,$clientId){
			$orderInfo = $this->chipsOrderDb->getOrderList($state,$clientId);
			//处理一下订单列表信息
			foreach ($orderInfo as $key => $value) {
				$picInfo = $this->db->select('icon,newprice')->from('t_chips')->where('chips_id',$value['chips_id'])->get()->result_array();
				$orderInfo[$key]['icon'] = $picInfo[0]['icon'];
				$orderInfo[$key]['newprice'] = $picInfo[0]['newprice'];
				if($value['status'] == 1){
					$orderInfo[$key]['show_status'] = '未支付';
				}elseif($value['status'] == 2){
					$orderInfo[$key]['show_status'] = '待结束';
				}elseif($value['status'] == 3){
					$orderInfo[$key]['show_status'] = '全额支付完毕';
				}
			}
			return $orderInfo;
		}

		//获取订单数量
		public function getOrderCount($userId,$clientId){
			return $this->chipsOrderDb->getOrderCount($userId,$clientId);
		}

		//检测有无未支付的预付订单
		public function chipsOrderStatus($clientId,$userId){
			return $this->chipsOrderDb->chipsOrderStatus($clientId,$userId);
		}

		//下单
		public function down($clientId,$userId,$chips_id,$num,$addressId,$name,$province,$city,$address,$phone){
			if($this->chipsAo->getStart($chips_id) != 1){
				throw new CI_MyException(1,'该商品不在可众筹阶段');
			}

			if($this->chipsOrderStatus($clientId,$userId) == false){
				throw new CI_MyException(1,'请先支付生成的预付订单后再重新下单');
			}
			//先处理web端下单
			$chipsInfo = $this->chipsAo->chipsDetail($chips_id,$userId);

			$orderNo = '';
			//订单号
			while (1) {
				$orderNo = date('YmdHis',time()).$clientId.mt_rand(0,100);
				$orderNoResult = $this->chipsOrderDb->checkOrderNo($orderNo);
				if($orderNoResult == true){
					break;
				}else{
					continue;
				}
			}
			$data['orderNo'] = $orderNo;
			$data['num'] = $num;
			$data['userId'] = $userId;
			$data['clientId'] = $clientId;
			$data['unit_price'] = $chipsInfo['newprice'];
			$data['percent']  = $chipsInfo['percent'];
			$data['firstpay'] = $chipsInfo['newprice'] * $chipsInfo['percent'] * 0.01 * $num;
			$data['firstpay'] = sprintf("%.2f", $data['firstpay']);  //处理价格 保留两位小数
			$data['chips_id'] = $chips_id;
			$data['time']     = time();
			$data['name'] = $name;
			$data['province'] = $province;
			$data['city'] = $city;
			$data['address'] = $address;
			$data['phone'] = $phone;
			$data['down_time'] = date('Y-m-d H:i:s',time());
			//插入web端订单
			$chips_order_id = $this->chipsOrderDb->addChipsOrder($data);

			//获取订单信息
			$orderInfo = $this->chipsOrderDb->getOrderInfo($chips_order_id);

			//微信统一下单
			$this->chipsOrderDb->addWxOrder($userId,$clientId,$orderInfo);

			return $chips_order_id;
		}

		//支付预付
		public function wxJsPay($userId,$chips_order_id){
			$orderInfo = $this->chipsOrderDb->getOrderInfo($chips_order_id);
			return $this->pay->wxJsPay($userId,$orderInfo['wxPrePayId']);
		}

		//支付余额
		public function payAll($userId,$clientId,$chips_id,$chips_order_id){
			//获取订单信息
			$orderInfo = $this->chipsOrderDb->getOrderInfo($chips_order_id);
			//获取商品信息
			$chipsInfo = $this->chipsAo->chipsDetail($chips_id,$userId);
			if(!empty($orderInfo['wxPrePayId2'])){
				return $this->pay->wxJsPay($userId,$orderInfo['wxPrePayId2']);
			}else{
				//获取商品信息
				$newprice  = $chipsInfo['newprice'];
				$orderNo   = $orderInfo['orderNo'].'00002'; //提交到微信下单的订单号
				$total_fee = ($newprice * $orderInfo['num'] - $orderInfo['firstpay']);
				$total_fee = sprintf("%2.f",$total_fee) * 100; //提交到微信下单的支付金额
				$wxorderInfo = $this->pay->wxPay($userId,$clientId,$orderNo,'pay_all',$total_fee);
				//更新订单的prepay_id2
				$data['wxPrePayId2'] = $wxorderInfo['prepay_id'];
				$data['end_unit_price'] = $newprice;
				$this->chipsOrderDb->updateOrder($chips_order_id,$data);
				//wxJsPay
				return $this->pay->wxJsPay($userId,$wxorderInfo['prepay_id']);
			}
		}

		//订单页面 进行支付预付
		public function orderFirstPay($userId,$chips_order_id){
			$orderInfo = $this->chipsOrderDb->getOrderInfo($chips_order_id);
			$chips_id  = $orderInfo['chips_id'];
			$start     = $this->chipsAo->getStart($chips_id);
			if($start != 1){
				//删除该订单
				$this->chipsOrderDb->delOrder($chips_order_id);
				throw new CI_MyException(1,'该商品的众筹时间已经结束');
			}
			$wxPrePayId = $orderInfo['wxPrePayId'];
			return $this->pay->wxJsPay($userId,$wxPrePayId);
		}

		//支付预付后
		public function firstPayOver($chips_order_id){
			$data['status'] = 2;
			$data['pay_first_time'] = date('Y-m-d H:i:s',time());
			$this->chipsOrderDb->updateOrder($chips_order_id,$data);
		}

		//全额支付后
		public function payAllAfter($chips_order_id){
			return $this->chipsOrderDb->payAllAfter($chips_order_id);
		}

		//重新支付预付
		public function rePayFirst($userId,$chips_order_id){
			$orderInfo = $this->chipsOrderDb->getOrderInfo($chips_order_id);
			$chips_id  = $orderInfo[0]['chips_id'];
			$start     = $this->chipsAo->getStart($chips_id);
			if($start != 1){
				//删除该订单
				$this->chipsOrderDb->delOrder($chips_order_id);
				throw new CI_MyException(1,'该商品的众筹时间已经结束');
			}
			//微信统一下单
			$orderNo = $orderInfo['orderNo'].'00011';
			$total_fee = $orderInfo['firstpay'] * 100;
			$wxorderInfo = $this->pay->wxPay($userId,$orderInfo['clientId'],$orderNo,'buy_some',$total_fee);
			//更新order 的PrePayId
			$data['wxPrePayId'] = $wxorderInfo['prepay_id'];
			$this->chipsOrderDb->updateOrder($chips_order_id,$data);
			//发起支付
			return $this->pay->wxJsPay($userId,$wxorderInfo['prepay_id']);
		}

		//重新支付余额
		public function rePayAll($userId,$chips_order_id,$chips_id){
			$orderInfo = $this->chipsOrderDb->getOrderInfo($chips_order_id);
			//获取商品信息
			$chipsInfo = $this->chipsAo->chipsDetail($chips_id,$userId);
			//微信统一下单
			$newprice= $chipsInfo['newprice'];
			$orderNo = $orderInfo['orderNo'].'00022';
			$total_fee = ($newprice * $orderInfo['num'] - $orderInfo['firstpay']);
			$total_fee = sprintf("%2.f",$total_fee) * 100; //提交到微信下单的支付金额
			$wxorderInfo = $this->pay->wxPay($userId,$orderInfo['clientId'],$orderNo,'pay_all',$total_fee);
			//更新订单的prepay_id2
			$data['wxPrePayId2'] = $wxorderInfo['prepay_id'];
			$data['end_unit_price'] = $newprice;
			$this->chipsOrderDb->updateOrder($chips_order_id,$data);
			//wxJsPay
			return $this->pay->wxJsPay($userId,$wxorderInfo['prepay_id']);
		}
	}
 ?>