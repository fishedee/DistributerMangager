<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionOrderWhen extends CI_Model
{
    parent::__construct(){
        parent::__construct();
        $this->load->model('distribution/distributionOrderDb', 'distributionOrderDb');
        $this->load->model('distribution/distributionCommodityAo', 'distributionCommodityAo');
        $this->load->model('order/orderCommodityAo', 'orderCommodityAo');
        $this->load->model('order/orderAo', 'orderAo');
    }

    public function whenGenerateOrder($entranceUserId, $shopOrderId){
        $order = $this->orderAo->get($shopOrderId);                             
        $clientId = $order['clientId'];
        $linkUsers = $this->distributionAo->getLink($userId, $entranceUserId);
        $data = array(
            'price'=>1,
            'shopOrderId'=>$shopOrderId,
            'state'=>$this->distributionOrderStateEnum->UN_PAY
        );

        for($i = 0; $i < count($linkUsers)-1; ++$i){
            $distributionOrderId = $this->distributionOrderDb->add($linkUsers[$i], 
                $linkUsers[$i + 1], $data);

            $where = array(
                'shopOrderId'=>$shopOrderId
            );

            $response = $this->orderCommodityAo->search($where, array());
            $commoditys = $response['data'];
            foreach($commoditys as $commodity){
                $data = array(
                    'distributionOrderId' =>$distributionOrderId,
                    'shopOrderId'=>$shopOrderId,
                    'shopCommodityId'=>$commodity['shopCommodityId'],
                    'price'=>0
                );
            $this->distributionCommodityAo->add($data);
        }
    }
}
