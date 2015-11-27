<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityCommentAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('shop/commodityCommentDb','commodityCommentDb');
		$this->load->model('shop/commodityAo','commodityAo');
		$this->load->model('order/orderAo','orderAo');
		$this->load->model('order/orderStateEnum','orderStateEnum');
		$this->load->model('client/clientAo','clientAo');
	}

	public function search($userId,$shopCommodityId,$dataWhere, $dataLimit){
		// $this->commodityAo->get($userId,$shopCommodityId);
		// echo 1;die;
		$dataWhere['shopCommodityId'] = $shopCommodityId;
		$info = $this->commodityCommentDb->search($dataWhere, $dataLimit);
		$data = $info['data'];
		foreach ($data as $key => $value) {
			$clientInfo = $this->clientAo->get($userId,$value['clientId']);
			$data[$key]['nickName'] = base64_decode($clientInfo['nickName']);
			$data[$key]['headImgUrl'] = $clientInfo['headImgUrl'];
		}
		$info['data'] = $data;
		return $info;
	}

	public function getComment($shopCommodityId){
		$info = $this->commodityCommentDb->getComment($shopCommodityId);
		foreach ($info as $key => $value) {
			$info[$key]['nickName'] = base64_decode($value['nickName']);
		}
		return $info;
	}

	//评论
	public function comment($userId,$clientId,$shopOrderId,$shopOrderCommodityId,$content){
		//获取订单明细的信息
		$shopOrderCommodityInfo = $this->orderAo->getOrderCommodity($shopOrderCommodityId);
		//比较订单流水号信息
		if($shopOrderCommodityInfo['shopOrderId'] != $shopOrderId){
			throw new CI_MyException(1,'无效订单流水号');
		}
		if($shopOrderCommodityInfo['comment'] != 0){
			throw new CI_MyException(1,'该商品已经评价');
		}
		//获取订单信息
		$orderInfo = $this->orderAo->get($shopOrderId);
		//判断订单状态
		if($orderInfo['state'] != $this->orderStateEnum->HAS_RECEIVED){
			throw new CI_MyException(1,'该订单不处于可评价阶段');
		}
		$data['clientId'] = $clientId;
		$data['shopCommodityId'] = $shopOrderCommodityInfo['shopCommodityId'];
		$data['content'] = $content;
		$result = $this->commodityCommentDb->add($data);
		if($result){
			//更改订单明细状态
			$result = $this->orderAo->modOrderCommodity($shopOrderCommodityId);
			if($result){
				$result = $this->orderAo->checkComment($shopOrderId);
				if($result){
					return 1;
				}else{
					//订单明细已经全部评论了 订单更改状态成 已经评价
					$data = array();
					$data['state'] = $this->orderStateEnum->HAS_COMMENT;
					return $this->orderAo->mod($shopOrderId,$data);
				}
			}else{
				throw new CI_MyException(1,'订单明细评价状态更改失败');
			}
		}else{
			throw new CI_MyException(1,'发表评价失败');
		}
	}

	//获取评论详情
	public function getCommentDetail($commentId){
		$commentInfo = $this->commodityCommentDb->getCommentDetail($commentId);
		if($commentInfo){
			return $commentInfo[0];
		}else{
			throw new CI_MyException(1,'无效评论id');
		}
	}

	//删除评论
	public function del($userId,$commentId){
		$commentInfo = $this->getCommentDetail($commentId);
		$shopCommodityId = $commentInfo['shopCommodityId'];
		$commodityInfo = $this->commodityAo->get($userId,$shopCommodityId);
		return $this->commodityCommentDb->del($commentId);
	}
}
