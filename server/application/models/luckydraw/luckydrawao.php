<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LuckyDrawAo extends CI_Model 
{

	public function __construct(){
		parent::__construct();
		$this->load->model('luckydraw/luckyDrawDb','luckyDrawDb');
		$this->load->model('luckydraw/luckyDrawCommodityDb','luckyDrawCommodityDb');
		$this->load->model('luckydraw/luckyDrawClientDb','luckyDrawClientDb');
		$this->load->model('luckydraw/luckyDrawStateEnum','luckyDrawStateEnum');
		$this->load->model('luckydraw/luckyDrawTypeEnum','luckyDrawTypeEnum');
		$this->load->model('luckydraw/conponAo','conponAo');
	}

	private function check($userId,$data){
		if( $data['state'] != $this->luckyDrawStateEnum->ON_STORAGE )
			return;
		if( $data['method'] == 1 && count($data['commodity']) != 8 )//大抽奖
			throw new CI_MyException(1,'必须选择刚好8个抽奖商品');
		foreach( $data['commodity'] as $singleCommodity ){
			if( $singleCommodity['type'] == $this->luckyDrawTypeEnum->COMMODITY ){
				//普通抽奖商品
			}else if( $singleCommodity['type'] == $this->luckyDrawTypeEnum->THANKYOU ){
				//谢谢抽奖
			}else if( $singleCommodity['type'] == $this->luckyDrawTypeEnum->COUPON ){
				$this->conponAo->queryCouponStock($userId,$singleCommodity);
			}else if( $singleCommodity['type'] == $this->luckyDrawTypeEnum->CARD){
				//卡券
			}else{
				//抛出异常
				throw new CI_MyException(1,'不合法的抽奖类型');
			}
			if( $singleCommodity['title'] == '' )
				throw new CI_MyException(1,'抽奖商品的标题不能为空');
			if( $singleCommodity['image'] == '' )
				throw new CI_MyException(1,'抽奖商品的图片不能为空');
			if( $singleCommodity['quantity'] < 0 )
				throw new CI_MyException(1,'抽奖商品的数量必须要大于或等于0');
		}
		if( $data['beginTime'] >= $data['endTime'] )
			throw new CI_MyException(1,'开始时间必须少于结束时间');
	}

	private function filterInputData($data){
		$data['beginTime'] .= ' 00:00:00';
		$data['endTime'] .= ' 23:59:59';
		return $data;
	}

	private function filterOutputData($data){
		$data['link'] = 'http://'.$data['userId'].'.'.$_SERVER['HTTP_HOST'].'/'.$data['userId'].'/lucky.html?luckyDrawId='.$data['luckyDrawId'];
		$data['beginTime'] = substr($data['beginTime'],0,10);
		$data['endTime'] = substr($data['endTime'],0,10);
		$data['totalQuantity'] = 0;
		foreach( $data['commodity'] as $key=>$singleCommodity ){
			$data['commodity'][$key]['typeName'] = $this->luckyDrawTypeEnum->names[$data['commodity'][$key]['type']];
			$data['totalQuantity'] += $singleCommodity['quantity'];
		}
		return $data;
	}

	public function search($userId,$where,$limit){
		$where['userId'] = $userId;
		return $this->luckyDrawDb->search($where,$limit);
	}

	public function get($userId,$luckyDrawId){
		//拉取抽奖信息
		$luckyDraw = $this->luckyDrawDb->get($luckyDrawId);
		if( $luckyDraw['userId'] != $userId )
			throw new CI_MyException(1,'没有权限查看此抽奖活动');
		$luckyDraw['commodity'] = $this->luckyDrawCommodityDb->getByLuckyDrawId($luckyDrawId);

		//过滤输出
		$luckyDraw = $this->filterOutputData($luckyDraw);
		return $luckyDraw;
	}

	public function add($userId,$data){
		//过滤输入
		$data = $this->filterInputData($data);

		//校验抽奖
		$this->check($userId,$data);

		//添加抽奖
		$data['userId'] = $userId;
		$data['state'] = $this->luckyDrawStateEnum->OFF_STORAGE;
		$luckyDrawId = $this->luckyDrawDb->add($data);

		//添加抽奖商品
		$luckyDrawCommodity = array();
		$sort = 1 ;
		foreach( $data['commodity'] as $singleCommodity ){
			$luckyDrawCommodity[] = array_merge(
				$singleCommodity,
				array(
					'luckyDrawId'=>$luckyDrawId,
					'sort'=>$sort++
				)
			);
		}
		$this->luckyDrawCommodityDb->addBatch($luckyDrawCommodity);

		return $luckyDrawId;
	}

	public function mod($userId,$luckyDrawId,$data){
		//校验权限
		$originData = $this->get($userId,$luckyDrawId);

		//过滤输入
		$data = $this->filterInputData($data);

		//校验抽奖
		$this->check($userId,$data);

		//修改抽奖
		$this->luckyDrawDb->mod($luckyDrawId,$data);

		//修改抽奖商品
		$luckyDrawCommodity = array();
		$sort = 1 ;
		foreach( $data['commodity'] as $singleCommodity ){
			$luckyDrawCommodity[] = array_merge(
				$singleCommodity,
				array(
					'luckyDrawId'=>$luckyDrawId,
					'sort'=>$sort++
				)
			);
		}
		$this->luckyDrawCommodityDb->delByLuckyDrawId($luckyDrawId);
		$this->luckyDrawCommodityDb->addBatch($luckyDrawCommodity);

		return $luckyDrawId;
	}

	public function del($userId,$luckyDrawId){
		//校验权限
		$originData = $this->get($userId,$luckyDrawId);

		//删除抽奖和商品
		$this->luckyDrawDb->del($luckyDrawId);
		$this->luckyDrawCommodityDb->delByLuckyDrawId($luckyDrawId);
	}

	public function getResult($userId,$luckyDrawId){
		//校验权限
		$luckDraw = $this->get($userId,$luckyDrawId);

		return $this->luckyDrawClientDb->getByLuckyDrawId($luckyDrawId);
	}

	public function getClientResult($userId,$clientId,$luckyDrawId){
		//获取抽奖信息
		$luckyDraw = $this->get($userId,$luckyDrawId);

		//获取用户抽奖结果
		$luckyDrawClient = $this->luckyDrawClientDb->getByLuckyDrawAndClientId($luckyDrawId,$clientId);
		if( count($luckyDrawClient) != 0 )
			$luckyDraw['client'] = $luckyDrawClient[0];

		return $luckyDraw;
	}

	public function luckyDraw($userId,$clientId,$luckyDrawId,$name='',$phone='',$isCheckNameAndPhone=true){
		//校验权限
		$luckyDraw = $this->getClientResult($userId,$clientId,$luckyDrawId);
		if( $luckyDraw['state'] != $this->luckyDrawStateEnum->ON_STORAGE )
			throw new CI_MyException(1,'抽奖活动还未上架噢');
		$now = time();
		if( strtotime($luckyDraw['beginTime']) > $now )
			throw new CI_MyException(1,'抽奖活动还没开始呢');
		if( strtotime($luckyDraw['endTime']) < $now )
			throw new CI_MyException(1,'抽奖活动已经结束啦');
		if( isset($luckyDraw['client']) )
			throw new CI_MyException(1,'你已经参与过这次抽奖活动，不能重复参与了');
		
		if ($isCheckNameAndPhone == true) {
			if( strlen($name) == 0 )
	            throw new CI_MyException(1,'请输入名字');
			if(preg_match_all('/^\d{11}$/',$phone) == 0 )
	            throw new CI_MyException(1,'请输入11位数字的电话号码以便获得抽奖奖品噢');
		}


		//校验商品数量
		$totalQuantity = 0;
		foreach( $luckyDraw['commodity'] as $singleCommodity)
			$totalQuantity += $singleCommodity['quantity'];
		if( $totalQuantity == 0 )
			throw new CI_MyException(1,'迟来一步了，抽奖活动的商品都被人抽光啦');


		//使用随机数选取合适的奖品
		$randNum = mt_rand(1,$totalQuantity);
		$currentCommodity = null;
		$currentQuantity = 0;
		foreach( $luckyDraw['commodity'] as $key=>$singleCommodity ){
			$currentQuantity += $singleCommodity['quantity'];
			if( $currentQuantity >= $randNum ){
				$currentCommodity = $singleCommodity;
				break;
			}
		}
		if( $currentCommodity == null )
			throw new CI_MyException(1,'计算抽奖结果出错，请联系后台工作人员');
		
		//记录抽奖结果
		$this->luckyDrawCommodityDb->reduceQuantity(
			$currentCommodity['luckyDrawCommodityId']
		);
		$luckyDrawClientId=$this->luckyDrawClientDb->add(array(
			'luckyDrawId'=>$luckyDrawId,
			'clientId'=>$clientId,
			'title'=>$currentCommodity['title'],
			'image'=>$currentCommodity['image'],
			'type'=>$currentCommodity['type'],
			'card_id'=>$currentCommodity['card_id'],
			'name'=>$name,
			'phone'=>$phone
		));
		
		//代金券处理
		if( $currentCommodity['coupon_id'] >0 && $currentCommodity['typeName'] == '代金券' )
			$this->conponAo->sendToCoupon($userId,$clientId,$luckyDrawClientId,$currentCommodity['coupon_id']);
		
		return $currentCommodity['luckyDrawCommodityId'];
	}

	public function getClientAllResult($userId,$clientId){
		return $this->luckyDrawClientDb->getByClientId($clientId);
	}

	//注销
	public function withDraw($list_id){
		return $this->luckyDrawClientDb->withDraw($list_id);
	}

	//判断合理性
	public function judge($list_id){
		return $this->luckyDrawClientDb->judge($list_id);
	}
}
