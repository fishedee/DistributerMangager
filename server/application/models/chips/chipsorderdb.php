<?php 
/**
 * author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsOrderDb extends CI_Model{
		private $tableName = 't_chips_order';

		public function __construct(){
			parent::__construct();
			// $this->load->database($this->tableName);
			$this->load->model('chips/chipsDb','chipsmodel');
			$this->load->model('chips/chipsOrderPayDb','pay');
			$this->load->model('chips/chipsRecordDb','record');
		}

		//检测订单 查看 有无之前订购 却还没支付的
		public function chipsOrderStatus($clientId,$userId){
			$condition['clientId'] = $clientId;
			$condition['userId']   = $userId;
			$condition['status']   = 1;
			$result = $this->db->select('chips_order_id')->from($this->tableName)->where($condition)->get()->result_array();
			if(empty($result)){
				return true;
			}else{
				return false;
			}
		}

		//获取订单详细信息
		public function getOrderInfo($chips_order_id){
			$this->db->where('chips_order_id',$chips_order_id);
			$orderInfo = $this->db->get($this->tableName)->result_array();
			return $orderInfo[0];
		}

		//插入我的订单
		public function addChipsOrder($data){
			$this->db->insert($this->tableName,$data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}

		//检测订单号是否重复
		public function checkOrderNo($orderNo){
			$result = $this->db->select('chips_order_id')->from($this->tableName)->where('orderNo',$orderNo)->get()->result_array();
			if($result){
				return false;
			}else{
				return true;
			}
		}

		//微信下单
		public function addWxOrder($userId,$clientId,$orderInfo){
			//提交到微信的orderno
			$orderNo = $orderInfo['orderNo'].'00001';
			$total_fee = $orderInfo['firstpay'] * 100;
			$wxorderInfo = $this->pay->wxPay($userId,$clientId,$orderNo,'buy_some',$total_fee);
			//更新order 的 wxPrePayId
			$this->db->where('chips_order_id',$orderInfo['chips_order_id']);
			$data['wxPrePayId'] = $wxorderInfo['prepay_id'];
			$this->db->update($this->tableName,$data);
		}

		//更新订单信息
		public function updateOrder($chips_order_id,$data){
			$this->db->where('chips_order_id',$chips_order_id);
			$this->db->update($this->tableName,$data);
			return $this->db->affected_rows();
		}

		//删除订单
		public function delOrder($chips_order_id){
			$this->db->delete($this->tableName,array('chips_order_id'=>$chips_order_id));
			return $this->db->affected_rows();
		}

		//下单
		// public function down($clientId,$userId,$chips_id,$order_num,$addressId,$name,$province,$city,$address,$phone){
		// 	//判断状态
		// 	$start = $this->chipsmodel->getStart($chips_id);
		// 	if($start != 1){
		// 		throw new CI_MyException(1,'该商品不在可众筹状态');
		// 	}
		// 	//插入我的订单
		// 	// $order_id = $this->addChipsOrder($clientId,$userId,$chips_id,$order_num,$addressId,$name,$province,$city,$address,$phone);

		// 	//根据订单id查询订单信息
		// 	$this->db->where('chips_order_id',$order_id);
		// 	$orderInfo = $this->db->get($this->tableName)->result_array();
		// 	$orderInfo = $orderInfo[0];

		// 	//微信统一下单
		// 	$this->addWxOrder($userId,$clientId,$orderInfo);

		// 	//返回orderid
		// 	return $order_id;
		// }

		//支付预付 订单过期 重新微信下单
		// public function rePayFirst($userId,$chips_order_id){
		// 	$chipsInfo = $this->db->select('chips_id')->from($this->tableName)->where('chips_order_id',$chips_order_id)->get()->result_array();
		// 	$chips_id  = $chipsInfo[0]['chips_id'];
		// 	$start     = $this->chipsmodel->getStart($chips_id);
		// 	if($start != 1){
		// 		//删除该订单
		// 		$this->db->where('chips_order_id',$chips_order_id);
		// 		$this->db->delete($this->tableName,array('chips_order_id'=>$chips_order_id));
		// 		throw new CI_MyException(1,'该商品的众筹时间已经结束');
		// 	}
		// 	$this->db->where('chips_order_id',$chips_order_id);
		// 	$orderInfo = $this->db->get($this->tableName)->result_array();
		// 	$orderInfo = $orderInfo[0];
		// 	//微信统一下单
		// 	$orderNo = $orderInfo['orderNo'].'00011';
		// 	$total_fee = $orderInfo['firstpay'] * 100;
		// 	$wxorderInfo = $this->pay->wxPay($userId,$orderInfo['clientId'],$orderNo,'buy_some',$total_fee);
		// 	//更新order 的PrePayId
		// 	$this->db->where('chips_order_id',$orderInfo['chips_order_id']);
		// 	$data['wxPrePayId'] = $wxorderInfo['prepay_id'];
		// 	$this->db->update($this->tableName,$data);
		// 	//发起支付
		// 	return $this->pay->wxJsPay($userId,$wxorderInfo['prepay_id']);
		// }

		//支付余额 订单过期 重新微信下单
		// public function rePayAll($userId,$chips_order_id,$chips_id){
		// 	$this->db->where('chips_order_id',$chips_order_id);
		// 	$orderInfo = $this->db->get($this->tableName)->result_array();
		// 	$orderInfo = $orderInfo[0];
		// 	//获取商品信息
		// 	$chipsInfo = $this->db->select('newprice')->from('t_chips')->where('chips_id',$chips_id)->get()->result_array();
		// 	$newprice  = $chipsInfo[0]['newprice'];
		// 	//微信统一下单
		// 	$orderNo = $orderInfo['orderNo'].'00022';
		// 	$total_fee = ($newprice * $orderInfo['num'] - $orderInfo['firstpay']);
		// 	$total_fee = sprintf("%2.f",$total_fee) * 100; //提交到微信下单的支付金额
		// 	$wxorderInfo = $this->pay->wxPay($userId,$orderInfo['clientId'],$orderNo,'pay_all',$total_fee);
		// 	//更新订单的prepay_id2
		// 	$this->db->where('chips_order_id',$chips_order_id);
		// 	$data['wxPrePayId2'] = $wxorderInfo['prepay_id'];
		// 	$data['end_unit_price'] = $newprice;
		// 	$this->db->update($this->tableName,$data);
		// 	//wxJsPay
		// 	return $this->pay->wxJsPay($userId,$wxorderInfo['prepay_id']);
		// }

		// //支付余额
		// public function payAll($userId,$clientId,$chips_id,$chips_order_id){
		// 	//获取订单信息
		// 	$this->db->where('chips_order_id',$chips_order_id);
		// 	$orderInfo = $this->db->get($this->tableName)->result_array();
		// 	$orderInfo = $orderInfo[0];
		// 	if(!empty($orderInfo['wxPrePayId2'])){
		// 		return $this->pay->wxJsPay($userId,$orderInfo['wxPrePayId2']);
		// 	}else{
		// 		//获取商品信息
		// 		$chipsInfo = $this->db->select('newprice')->from('t_chips')->where('chips_id',$chips_id)->get()->result_array();
		// 		$newprice  = $chipsInfo[0]['newprice'];
		// 		$orderNo   = $orderInfo['orderNo'].'00002'; //提交到微信下单的订单号
		// 		$total_fee = ($newprice * $orderInfo['num'] - $orderInfo['firstpay']);
		// 		$total_fee = sprintf("%2.f",$total_fee) * 100; //提交到微信下单的支付金额
		// 		$wxorderInfo = $this->pay->wxPay($userId,$clientId,$orderNo,'pay_all',$total_fee);
		// 		//更新订单的prepay_id2
		// 		$this->db->where('chips_order_id',$chips_order_id);
		// 		$data['wxPrePayId2'] = $wxorderInfo['prepay_id'];
		// 		$data['end_unit_price'] = $newprice;
		// 		$this->db->update($this->tableName,$data);
		// 		//wxJsPay
		// 		return $this->pay->wxJsPay($userId,$wxorderInfo['prepay_id']);
		// 	}
		// }

		// //订单已经生成 重新支付
		// public function orderFirstPay($userId,$chips_order_id){
		// 	$chipsInfo = $this->db->select('chips_id')->from($this->tableName)->where('chips_order_id',$chips_order_id)->get()->result_array();
		// 	$chips_id  = $chipsInfo[0]['chips_id'];
		// 	$start     = $this->chipsmodel->getStart($chips_id);
		// 	if($start != 1){
		// 		//删除该订单
		// 		$this->db->where('chips_order_id',$chips_order_id);
		// 		$this->db->delete($this->tableName,array('chips_order_id'=>$chips_order_id));
		// 		throw new CI_MyException(1,'该商品的众筹时间已经结束');
		// 	}
		// 	$orderInfo = $this->db->select('wxPrePayId')->from($this->tableName)->where('chips_order_id',$chips_order_id)->get()->result_array();
		// 	$wxPrePayId = $orderInfo[0]['wxPrePayId'];
		// 	return $this->pay->wxJsPay($userId,$wxPrePayId);
		// }

		//全额支付完成
		public function payAllAfter($chips_order_id){
			$orderInfo = $this->db->select('chips_id,num,firstpay')->from($this->tableName)->where('chips_order_id',$chips_order_id)->get()->result_array();
			$orderInfo = $orderInfo[0];

			$chipsInfo = $this->db->select('newprice')->from('t_chips')->where('chips_id',$orderInfo['chips_id'])->get()->result_array();
			$newprice  = $chipsInfo[0]['newprice'];
			$total     = $newprice * $orderInfo['num'];
			$end_free  = sprintf("%2.f",$total) - $orderInfo['firstpay'];
			
			$data['status'] = 3;
			$data['pay_all_time'] = date('Y-m-d H:i:s',time());
			$data['end_free'] = $end_free;
			$this->db->where('chips_order_id',$chips_order_id);
			$this->db->update($this->tableName,$data);
			return $this->db->affected_rows();
		}

		// //支付预付
		// public function wxJsPay($userId,$chips_order_id){
		// 	$orderInfo = $this->db->select('wxPrePayId')->from($this->tableName)->where('chips_order_id',$chips_order_id)->get()->result_array();
		// 	$prepay_id = $orderInfo[0]['wxPrePayId'];
		// 	return $this->pay->wxJsPay($userId,$prepay_id);
		// }

		//获取订单列表的信息
		public function getOrderList($state,$clientId){
			if($state == 0){
				$condition = array();
			}elseif($state == 1){
				$condition['status'] = 1;
			}elseif($state == 2){
				$condition['status'] = 2;
			}elseif($state == 3){
				$condition['status'] = 3;
			}
			$condition['clientId'] = $clientId;
			$orderInfo = $this->db->from($this->tableName)->where($condition)->order_by('chips_order_id DESC')->get()->result_array();
			
			return $orderInfo;
		}

		//获取订单的数量
		public function getOrderCount($userId,$clientId){
			$condition['userId'] = $userId;
			$condition['clientId'] = $clientId;
			//全部
			$all = $this->db->where($condition)->get($this->tableName)->num_rows();
			// $all = count($all);
			//完成
			$condition['status'] = 3;
			$over = $this->db->where($condition)->get($this->tableName)->num_rows();
			// $over = count($over);
			//not
			$condition['status'] = 1;
			$not = $this->db->where($condition)->get($this->tableName)->num_rows();
			// $not = count($not);
			//first
			$condition['status'] = 2;
			$first = $this->db->where($condition)->get($this->tableName)->num_rows();
			// $first = count($first);
			$data[] = $all;
			$data[] = $not;
			$data[] = $first;
			$data[] = $over;
			return $data;
		}

		//后台查看订单
		public function search($dataWhere,$limit){
			if( isset($limit["pageIndex"]) && isset($limit["pageSize"])){
				$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
			}
			$count = $this->db->where($dataWhere)->count_all_results($this->tableName);
			if(count($dataWhere) > 1){
				$this->db->where('userId',$dataWhere['userId']);
				$this->db->like('name',$dataWhere['name'],'both');
			}else{
				$this->db->where('userId',$dataWhere['userId']);
			}
			$orderInfo = $this->db->get($this->tableName)->result_array();
			return array(
				'count' => $count,
				'data'  => $orderInfo
				);
		}
		
	}
 ?>