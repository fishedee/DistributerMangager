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
	}

	private function check($userId,$data){
		if( $data['state'] != $this->luckyDrawStateEnum->ON_STORAGE )
			return;
		if( count($data['commodity']) != 8 )
			throw new CI_MyException(1,'必须选择刚好8个抽奖商品');
		$totalPrecent = 0;
		foreach( $data['commodity'] as $singleCommodity ){
			if( $singleCommodity['type'] == $this->luckyDrawTypeEnum->COMMODITY ){
				//普通抽奖商品
			}else if( $singleCommodity['type'] == $this->luckyDrawTypeEnum->THANKYOU ){
				//谢谢抽奖
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
			if( $singleCommodity['precent'] < 0 )
				throw new CI_MyException(1,'抽奖商品的概率必须要大于或等于0');
			$totalPrecent += $singleCommodity['precent'];
		}
		if( $data['beginTime'] >= $data['endTime'] )
			throw new CI_MyException(1,'开始时间必须少于结束时间');
		if( $totalPrecent != 10000 )
			throw new CI_MyException(1,'抽奖商品的总概率必须刚好为100%');
	}

	private function filterInputData($data){
		$data['beginTime'] .= ' 00:00:00';
		$data['endTime'] .= ' 23:59:59';
		foreach( $data['commodity'] as $key=>$singleCommodity ){
			$data['commodity'][$key]['precent'] = intval($data['commodity'][$key]['precentShow']*100);
		}
		return $data;
	}

	private function filterOutputData($data){
		$data['beginTime'] = substr($data['beginTime'],0,10);
		$data['endTime'] = substr($data['endTime'],0,10);
		foreach( $data['commodity'] as $key=>$singleCommodity ){
			$data['commodity'][$key]['precentShow'] = sprintf('%.2f',$data['commodity'][$key]['precent']/100).'%';
			$data['commodity'][$key]['typeName'] = $this->luckyDrawTypeEnum->names[$data['commodity'][$key]['type']];
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

	public function luckyDraw($userId,$clientId,$luckyDrawId,$name,$phone){
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
		if( strlen($name) == 0 )
            throw new CI_MyException(1,'请输入名字');
		if(preg_match_all('/^\d{11}$/',$phone == 0 ))
            throw new CI_MyException(1,'请输入11位数字的电话号码以便获得抽奖奖品噢');

		//校验商品数量
		$totalQuantity = 0;
		foreach( $luckyDraw['commodity'] as $singleCommodity)
			$totalQuantity += $singleCommodity['quantity'];
		if( $totalQuantity == 0 )
			throw new CI_MyException(1,'迟来一步了，抽奖活动的商品都被人抽光啦');

		//整理抽奖的非空奖品
		$luckyDrawCommodity = array();
		$totalPrecent = 0;
		foreach( $luckyDraw['commodity'] as $singleCommodity ){
			if( $singleCommodity['quantity'] == 0 )
				continue;
			$luckyDrawCommodity[] = $singleCommodity;
			$totalPrecent += $singleCommodity['precent'];
		}
		foreach( $luckyDrawCommodity as $key=>$singleCommodity ){
			$luckyDrawCommodity[$key]['precent'] = $luckyDrawCommodity[$key]['precent'] /$totalPrecent*10000;
		}

		//使用随机数选取合适的奖品
		$randNum = mt_rand(0,10000);
		$currentCommodity = null;
		$currentPrecent = 0;
		foreach( $luckyDrawCommodity as $key=>$singleCommodity ){
			$currentPrecent += $singleCommodity['precent'];
			if( $currentPrecent >= $randNum )
				$currentCommodity = $singleCommodity;
		}
		if( $currentCommodity == null )
			throw new CI_MyException(1,'计算抽奖结果出错，请联系后台工作人员');

		//记录抽奖结果
		$this->luckyDrawCommodityDb->reduceQuantity(
			$currentCommodity['luckyDrawCommodityId']
		);
		$this->luckyDrawClientDb->add(array(
			'luckyDrawId'=>$luckyDrawId,
			'clientId'=>$clientId,
			'title'=>$currentCommodity['title'],
			'image'=>$currentCommodity['image'],
			'type'=>$currentCommodity['type'],
			'name'=>$name,
			'phone'=>$phone
		));

		return $currentCommodity['luckyDrawCommodityId'];
	}

	public function getClientAllResult($userId,$clientId){
		return $this->luckyDrawClientDb->getByClientId($clientId);
	}
}
