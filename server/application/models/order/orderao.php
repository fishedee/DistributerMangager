<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderAo extends CI_Model 
{

	public function __construct(){
		parent::__construct();
		$this->load->model('shop/commodityAo','commodityAo');
		$this->load->model('shop/trollerAo','trollerAo');
		$this->load->model('address/addressAo','addressAo');
		$this->load->model('client/clientAo','clientAo');
		$this->load->model('order/orderPayAo','orderPayAo');
		$this->load->model('user/userAppAo', 'userAppAo');
		$this->load->model('order/orderDb','orderDb');
		$this->load->model('order/orderCommodityDb','orderCommodityDb');
		$this->load->model('order/orderAddressDb','orderAddressDb');
		$this->load->model('order/orderStateEnum','orderStateEnum');
		$this->load->model('address/addressPayMentEnum','addressPayMentEnum');
		$this->load->model('common/commonErrorEnum', 'commonErrorEnum');
		$this->load->model('distribution/distributionOrderWhen', 'distributionOrderWhen');
	}

	private function addMyOrder($userId,$clientId,$shopCommodity,$address,$entranceUserId){
		//计算出订单基本信息
		$orderPrice = array_reduce($shopCommodity,function($sum,$single){
			return $sum + $single['price']*$single['quantity'];
		},0);
		$orderProducts = array_map(function($shopCommodity){
			return $shopCommodity['title'];
		},$shopCommodity);
		$orderNum = count($shopCommodity);
		$orderDesc = $address['name'].'购买的"'.implode('","', $orderProducts).'"';
		if($address['payment'] == $this->addressPayMentEnum->CODPAY)
			$orderState = $this->orderStateEnum->NO_SEND;
		else
			$orderState = $this->orderStateEnum->NO_PAY;

		//添加订单基本信息
		$shopOrderId = date('YmdHis').$clientId.rand(10000,99999);
		$orderInfo = array(
			'shopOrderId'=>$shopOrderId,
			'userId'=>$userId,
			'clientId'=>$clientId,
			'entranceUserId'=>$entranceUserId,
			'image'=>$shopCommodity[0]['icon'],
			'price'=>$orderPrice,
			'num'=>$orderNum,
			'name'=>$address['name'],
			'description'=>$orderDesc,
			'wxprePayId'=>0,
			'state'=>$orderState
		);
		$this->orderDb->add($orderInfo);

		//添加订单商品信息
		$this->orderCommodityDb->addBatch(array_map(function($singleShopCommodity)use($shopOrderId){
			return array(
				'shopOrderId'=>$shopOrderId,
				'shopCommodityId'=>$singleShopCommodity['shopCommodityId'],
				'title'=>$singleShopCommodity['title'],
				'icon'=>$singleShopCommodity['icon'],
				'introduction'=>$singleShopCommodity['introduction'],
				'price'=>$singleShopCommodity['price'],
				'oldPrice'=>$singleShopCommodity['oldPrice'],
				'quantity'=>$singleShopCommodity['quantity'],
			);
		},$shopCommodity));

		//添加订单地址信息
    	$this->orderAddressDb->add(array(
			'shopOrderId'=>$shopOrderId,
			'name'=>$address['name'],
			'province'=>$address['province'],
			'city'=>$address['city'],
			'address'=>$address['address'],
			'phone'=>$address['phone'],
			'payment'=>$address['payment'],
		));

    	return $orderInfo;
	}

	private function addWxOrder($userId,$clientId,$orderInfo){
		$wxOrderInfo = $this->orderPayAo->wxPay(
			$userId,
			$clientId,
			$orderInfo['shopOrderId'],
			$orderInfo['description'],
			$orderInfo['price']
		);

		$this->orderDb->mod(
			$orderInfo['shopOrderId'],
			array('wxPrePayId'=>$wxOrderInfo['prepay_id'])
		);
	}

	public function search($dataWhere,$dataLimit){
        $data = $this->orderDb->search($dataWhere,$dataLimit);
        foreach($data['data'] as $key=>$value ){
            $data['data'][$key]['priceShow'] = $this->commodityAo->getFixedPrice($data['data'][$key]['price']);
        }
        return $data;
	}

	public function getClientOrder($clientId){
		$orderNum = $this->orderDb->getCountByClientId($clientId);
		$result = array();

		foreach($orderNum as $single){
			$result[$single['state']] = intval($single['count']);
		}

		foreach($this->orderStateEnum->names as $key=>$value){
			if(isset($result[$key]) == false )
				$result[$key] = 0;
		}

		$result[0] = array_reduce($result,function($sum,$single){
			return $sum+$single;
		},0);
		return $result;
	}
	public function getClientOrderDetail($clientId,$state){
		$dataWhere = array('clientId'=>$clientId);
		if( $state != 0 )
			$dataWhere['state'] = $state;
		return $this->search($dataWhere,array())['data'];
	}

	public function getClientOrderDetail2($clientId,$state){
		$dataWhere = array('clientId'=>$clientId);
		if( $state != 0 )
			$dataWhere['state'] = $state;
		$shopOrder = $this->search($dataWhere,array())['data'];
		foreach ($shopOrder as $key => $value) {
			$info = $this->orderCommodityDb->getByShopOrderId($value['shopOrderId']);
			foreach ($info as $k => $v) {
				$info[$k]['showPrice'] = sprintf('%.2f',$v['price']/100);
				$info[$k]['sum'] = sprintf('%.2f',($v['price'] * $v['quantity'])/100);
			}
			$shopOrder[$key]['list'] = $info;
		}
		// var_dump($shopOrder);die;
		return $shopOrder;
	}

	public function get($shopOrderId){
		$shopOrder = $this->orderDb->get($shopOrderId);

		$shopOrder['priceShow'] = $this->commodityAo->getFixedPrice($shopOrder['price']);

		$shopOrder['address'] = $this->orderAddressDb->getByShopOrderId($shopOrderId)[0];

		$shopOrder['commodity'] = $this->orderCommodityDb->getByShopOrderId($shopOrderId);
		foreach($shopOrder['commodity'] as $key=>$value){
			$shopOrder['commodity'][$key]['priceShow'] = $this->commodityAo->getFixedPrice(
				$shopOrder['commodity'][$key]['price']
			);
		}
		
		//查快递信息
		if ($shopOrder['expressageName'] != 0 && $shopOrder['expressageNum'] != '')
			$shopOrder['expMsg'] = $this->getExpMsg($shopOrder['expressageName'],$shopOrder['expressageNum']);

		return $shopOrder;
	}

	public function add($entranceUserId,$clientId,$loginClientId,$shopTrollerId,$address){
		//获取购物车内的商品信息
		$shopTroller = $this->trollerAo->getByIds($clientId,$shopTrollerId);
		var_dump($shopTroller);die;
		//校验购物车内的商品信息
		foreach($shopTroller as $singleShopTroller){
			$this->trollerAo->check($singleShopTroller);
		}

		//校验地址信息
		$this->addressAo->check($address);

		//保存为默认收货地址
		$this->addressAo->mod($clientId,$address);
		
		//校验商品信息
		if( count($shopTroller) == 0 )
			throw new CI_MyException(1,'订单内的商品不能为空');
		foreach($shopTroller as $singleShopTroller)
			if($singleShopTroller['quantity'] <= 0 )
				throw new CI_MyException(1,'选择的商品数量不能为0');
		$userId = $shopTroller[0]['userId'];
		foreach($shopTroller as $singleShopTroller)
			if($singleShopTroller['userId'] != $userId)
				throw new CI_MyException (1,$this->commonErrorEnum->SHOP_CART_CHECK_ERROR,'只能购买同一商城内的商品');

		//扣库存
		foreach($shopTroller as $singleShopTroller ){
			$this->commodityAo->reduceStock($singleShopTroller['userId'],$singleShopTroller['shopCommodityId'],$singleShopTroller['quantity']);
		}

		//下单
		$orderInfo = $this->addMyOrder($userId,$clientId,$shopTroller,$address,$entranceUserId);

		//微信统一下单
		$this->addWxOrder($userId,$loginClientId,$orderInfo);

		//删购物车
		$this->trollerAo->delByIds($clientId,$shopTrollerId);

		//触发分成订单
		$this->distributionOrderWhen->whenGenerateOrder($entranceUserId,$this->get($orderInfo['shopOrderId']));

		return $orderInfo['shopOrderId'];
	}

	//新商城模板 不需要传递地址信息
	public function add2($entranceUserId,$clientId,$loginClientId,$shopTrollerId){
		if(!$shopTrollerId){
			throw new CI_MyException(1,'无效购物车信息');
		}
		// var_dump($shopTrollerId);die;
		//获取购物车内的商品信息
		$shopTroller = $this->trollerAo->getCartInfo($clientId,$shopTrollerId);
		// var_dump($shopTroller);die;
		//校验购物车内的商品信息
		foreach($shopTroller as $singleShopTroller){
			$this->trollerAo->check($singleShopTroller);
		}

		$address = $this->addressAo->get($clientId);

		//校验地址信息
		$this->addressAo->check($address);

		//保存为默认收货地址
		$this->addressAo->mod($clientId,$address);
		
		//校验商品信息
		if( count($shopTroller) == 0 )
			throw new CI_MyException(1,'订单内的商品不能为空');
		foreach($shopTroller as $singleShopTroller)
			if($singleShopTroller['quantity'] <= 0 )
				throw new CI_MyException(1,'选择的商品数量不能为0');
		$userId = $shopTroller[0]['userId'];
		foreach($shopTroller as $singleShopTroller)
			if($singleShopTroller['userId'] != $userId)
				throw new CI_MyException (1,$this->commonErrorEnum->SHOP_CART_CHECK_ERROR,'只能购买同一商城内的商品');

		//扣库存
		foreach($shopTroller as $singleShopTroller ){
			$this->commodityAo->reduceStock($singleShopTroller['userId'],$singleShopTroller['shopCommodityId'],$singleShopTroller['quantity']);
		}

		//下单
		$orderInfo = $this->addMyOrder($userId,$clientId,$shopTroller,$address,$entranceUserId);
		// var_dump($orderInfo);die;

		//微信统一下单
		$this->addWxOrder($userId,$loginClientId,$orderInfo);

		//删购物车
		$this->trollerAo->delByIds($clientId,$shopTrollerId);

		//触发分成订单
		$this->distributionOrderWhen->whenGenerateOrder($entranceUserId,$this->get($orderInfo['shopOrderId']));

		return $orderInfo['shopOrderId'];
	}

	public function againOrder($shopOrderId){

		$this->load->model('shop/trollerAo', 'trollerAo');

		$orderInfo = $this->get($shopOrderId);

		foreach ($orderInfo['commodity'] as $key => $value) {

			$this->trollerAo->addCommodity($orderInfo['clientId'],$value['shopCommodityId'],$value['quantity']);
		}

		$updataMsg = array('state'=>0);
		return $this->mod($shopOrderId,$updataMsg);
	}

	public function wxJsPay($clientId,$shopOrderId){
		$orderInfo = $this->get($shopOrderId);

		return $this->orderPayAo->wxJsPay($orderInfo['userId'],$orderInfo['wxPrePayId']);
	}

	public function modHasSend( $shopOrderId ){
        $shopOrder = $this->orderDb->get($shopOrderId);
        if($shopOrder['state'] == $this->orderStateEnum->NO_PAY)
         	throw new CI_MyException(1, "该订单未支付，不能扭转为已发货状态");

        $this->orderDb->mod($shopOrderId, array('state'=>$this->orderStateEnum->HAS_SEND));
        
        $this->load->model('shop/commodityAo','commodityAo');
        $priceShow = $this->commodityAo->getFixedPrice($shopOrder['price']);
        
        $this->load->model('address/addressDb', 'addressDb');
        $clientAddress = $this->addressDb->getByClientId($shopOrder['clientId'])[0];
        
        //发送消息模板,模板标题：订单标记发货通知，TM00015
        $sendData['url']='http://'.$_SERVER['HTTP_HOST'].'/'.$shopOrder['userId'].'/deal.html';
        $sendData['topcolor']='#FF0000';
        $sendData['data']['first']['value']="您的订单已经标记发货，请留意查收。\n >>查询订单物流状态";
        $sendData['data']['first']['color']='#173177';
        $sendData['data']['orderProductPrice']['value']=$priceShow;
        $sendData['data']['orderProductPrice']['color']='#173177';
        $sendData['data']['orderAddress']['value']=$clientAddress['province'].$clientAddress['city'].$clientAddress['address'];
        $sendData['data']['orderAddress']['color']='#173177';
        $sendData['data']['orderProductName']['value']=$shopOrder['description'];
        $sendData['data']['orderProductName']['color']='#173177';
        $sendData['data']['orderName']['value']=$shopOrderId;
        $sendData['data']['orderName']['color']='#173177';
        $sendData['data']['remark']['value']='欢迎再次购买！';
        $sendData['data']['remark']['color']='#173177';
        
        $this->load->model('weixin/wxTemplateAo','wxTemplateAo');
        $this->wxTemplateAo->notice($shopOrder['userId'],$shopOrder['clientId'],$sendData,'TM00505');
	}
	
	//获取快递信息
	public function getExpMsg($expressageName,$expressageNum){
		$this->load->model('order/expressageEnum','expressageEnum');
		$exp=$this->expressageEnum->enums;
		//获取快递公司拼音
		$expPY='';
		foreach ($exp as $k=>$v){
			if ($v[0] == $expressageName){
				$expPY = $v[1];
				break;
			}	
		}
		//print_r($expPY);die;
		
		//curl数据
		$this->load->library('http');
		$httpResponse = $this->http->ajax(array(
				'url'=>'http://www.kuaidi100.com/query?id=1&type='.$expPY.'&postid='.$expressageNum.'&valicode=&temp='.rand(1,99999),
				'type'=>'get',
				'data'=>array(),
				'dataType'=>'',
				'responseType'=>'json'
		));

		return $httpResponse['body']['data'];
	}

	//获取订单数量
	public function getOrderNum($userId,$entranceUserId){
		return $this->orderDb->getOrderNum($userId,$entranceUserId);
	}

	//获取订单明细
	public function getOrderCommodity($shopOrderCommodityId){
		$info = $this->orderCommodityDb->getOrderCommodity($shopOrderCommodityId);
		if($info){
			return $info[0];
		}else{
			throw new CI_MyException(1,'无效订单明细信息');
		}
	}

	//更新订单评论
	public function mod($shopOrderId,$data){
		// $data['state'] = $this->orderStateEnum->HAS_COMMENT;
		return $this->orderDb->mod($shopOrderId,$data);
	}


	public function modOrderCommodity($shopOrderCommodityId){
		$data['comment'] = 1;
		return $this->orderCommodityDb->mod($shopOrderCommodityId,$data);
	}

	// //更新订单收货
	// public function modReceive($shopOrderId,$data){
	// 	// $data['state'] = $this->orderStateEnum->HAS_SEND;
	// 	return $this->orderDb->mod($shopOrderId,$data);
	// }

	//检测评论数目
	public function checkComment($shopOrderId){
		$info = $this->orderCommodityDb->checkComment($shopOrderId);
		return count($info);
	}

	// //确认收货
	// public function confirmReceive($clientId,$shopOrderId,$shopOrderCommodityId){
	// 	//获取订单明细的信息
	// 	$shopOrderCommodityInfo = $this->getOrderCommodity($shopOrderCommodityId);
	// 	//获取订单信息
	// 	$orderInfo = $this->get($shopOrderId);
	// 	//判断订单状态
	// 	if($orderInfo['state'] != $this->orderStateEnum->HAS_SEND){
	// 		throw new CI_MyException(1,'该订单不处于可收货状态');
	// 	}
	// 	//检测订单合法性
	// 	if($orderInfo['clientId'] != $clientId){
	// 		throw new CI_MyException(1,'无效操作');
	// 	}
	// 	//比较订单流水号信息
	// 	if($shopOrderCommodityInfo['shopOrderId'] != $shopOrderId){
	// 		throw new CI_MyException(1,'无效订单流水号');
	// 	}
	// 	if($shopOrderCommodityInfo['receive'] != 0){
	// 		throw new CI_MyException(1,'该订单已经收货');
	// 	}
	// 	$data['receive'] = 1;
	// 	$data['receive'] = 1;
	// 	$result = $this->orderCommodityDb->mod($shopOrderCommodityId,$data);
	// 	if($result){
	// 		$result = $this->orderCommodityDb->checkReceive($shopOrderId);
	// 		if($result){
	// 			return 1;
	// 		}else{
	// 			$data = array();
	// 			$data['state'] = $this->orderStateEnum->HAS_RECEIVED;
	// 			return $this->mod($shopOrderId,$data);
	// 		}
	// 	}else{
	// 		throw new CI_MyException(1,'收货失败');
	// 	}
	// }

	public function confirmReceive($clientId,$shopOrderId,$shopOrderCommodityId){
		//获取订单信息
		$orderInfo = $this->get($shopOrderId);
		//判断订单状态
		if($orderInfo['state'] != $this->orderStateEnum->HAS_SEND){
			throw new CI_MyException(1,'该订单不处于可收货状态');
		}
		$data['state'] = $this->orderStateEnum->HAS_RECEIVED;
		$result = $this->mod($shopOrderId,$data);
		if($result){
			return $result;
		}else{
			throw new CI_MyException(1,'确认收货失败');
		}
	}
}
