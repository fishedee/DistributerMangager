<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionCommodityAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionCommodityDb', 'distributionCommodityDb');
	$this->load->model('distribution/distributionOrderDb', 'distributionOrderDb');
    }

    public function check($data){
        if( !isset($data['price']) || $data['price']  < 0)
            throw new CI_MyException(1, "商品金额不能为零");     
    }

    public function add($data){
        $this->check($data);
        $data['price'] = $data['price'] * 100;
        return $this->distributionCommodityDb->add($data);
    }

    public function mod($userId, $distributionCommodityId, $data){
        $this->check($data);
        if( isset($data['price']) )
            $data['price'] = $data['price'] * 100;
	$distributionCommodity = $this->distributionCommodityDb->get(
		$distributionCommodityId);
	$distributionOrder = $this->distributionOrderDb->get(
		$distributionCommodity['distributionOrderId']);
	if($distributionOrder['upUserId'] != $userId)
		throw new CI_MyException(1, "本用户无权限修改此分成商品");

        $this->distributionCommodityDb->mod($distributionCommodityId, $data);
    }


    public function get($distributionOrderId){
        $where = array(
            'distributionOrderId'=>$distributionOrderId
        );

        $response = $this->distributionCommodityDb->search(
            $where, array());
        $distributionCommodity = $response['data'];
        foreach($distributionCommodity as $key=>$value)
            $distributionCommodity[$key]['priceShow'] = $this->commodityAo->getFixedPrice($distributionCommodity[$key]['price']);

        return $distributionCommodity;
    }
}
