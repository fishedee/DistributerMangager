<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionOrderAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionorderDb', 'distributionOrderDb');
        $this->load->model('distribution/distributionAo', 'distributionAo');
        $this->load->model('order/orderAo', 'orderAo');
    }

    public function getFixedPrice($price){
        return sprintf("%.2f", $price/100);
    }

    public function search($where, $limit){
        $data = $this->distributionOrderDb->search($where, $limit);
        foreach($data['data'] as $key=>$value)
            $data['data'][$key]['priceShow'] = $this->getFixedPrice($value['price']);
        return $data;
    }

    public function get($distributionOrderId){
        $distributionOrder = $this->distributionOrderDb->get($distributionOrderId);
        $order = $this->orderAo->get($distributionOrder['shopOrderId']);
        $data = array(
            'distributionOrder'=>$distributionOrder,
            'shopOrder'=>$order
        );
        return $data;
    }

    public function add($upUserId, $downUserId, $data){
        $where = array(
            'upUserId'=>$upUserId,
            'downUserId'=>$downUserId
        );

        $response = $this->distributionAo->search($where, array());
        if($response['count'] == 0)
            throw new CI_MyException(1, '用户间不存在分成关系');
	$data['price'] = $data['price']*100;
        $this->distributionOrderDb->add($upUserId, $downUserId, $data);
    }

    public function mod($distributionOrderId, $data){
	if( isset($data['price']) ){
        	$data['price'] = $data['price']*100;   
        	if($data['price'] <= 0)
            		throw new CI_MyException(1, '价格不能少于或等于0');       
	}
        $this->distributionOrderDb->mod($distributionOrderId, $data);
    }

    public function payOrder($distributionOrderId){
        $order = $this->distributionOrderDb->get($distributionOrderId);
        if($order['state'] != $this->distributionOrderStateEnum->UN_PAY)
            throw new CI_MyException(1, '此分成不能设置付款中状态');

        $data = array(
            'state'=>$this->distributionOrderStateEnum->IN_PAY
        );
        $this->mod($distributionOrderId, $data);
    }

    public function hasPayOrder($distributionOrderId){
        $order = $this->distributionOrderDb->get($distributionOrderId);
        if($order['state'] != $this->distributionOrderStateEnum->IN_PAY)
            throw new CI_MyException(1, '此分成不能设置已付款状态'
        $data = array(
            'state'=>$this->distributionOrderStateEnum->HAS_PAY
        );
        $this->mod($distributionOrderId, $data);
    }

    public function confirm($distributionOrderId){
        $order = $this->distributionOrderDb->get($distributionOrderId);
        if($order['state'] != $this->distributionOrderStateEnum->HAS_PAY)
            throw new CI_MyException(1, '此分成不能设置确认付款状态');

        $data = array(
            'state'=>$this->distributionOrderStateEnum->HAS_CONFIRM
        );
        $this->mod($distributionOrderId, $data);
    }
     
    public function del($distributionOrderId){
        $this->distributionOrderDb->del($distributionOrderId);
    }

    public function whenGenerateOrder($entranceUserId, $shopOrderId){
        $order = $this->orderAo->get($shopOrderId);                             
        $clientId = $order['clientId'];
        $linkUsers = $this->distributionAo->getLink($clientId, $entranceUserId);
        $data = array(
            'price'=>1,
            'shopOrderId'=>$shopOrderId,
            'state'=>$this->distributionOrderStateEnum->UN_PAY
        );

        for($i = 0; $i < count($linkUsers)-1; ++$i){
            $this->add($linkUsers[$i], $linkUsers[$i + 1], $data);
        }        
    }
}
