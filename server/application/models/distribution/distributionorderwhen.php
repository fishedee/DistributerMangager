<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionOrderWhen extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionOrderDb', 'distributionOrderDb');
        $this->load->model('distribution/distributionCommodityAo', 'distributionCommodityAo');
        $this->load->model('order/orderCommodityAo', 'orderCommodityAo');
    }

    public function whenGenerateOrder($entranceUserId, $shopOrder){
        $clientId = $shopOrder['clientId'];
        $linkUsers = $this->distributionAo->getLink($userId, $entranceUserId);
        $data = array(
            'price'=>0,
            'shopOrderId'=>$shopOrder['shopOrderId'],
            'state'=>$this->distributionOrderStateEnum->UN_PAY
        );

        for($i = 0; $i < count($linkUsers)-1; ++$i){
            $distributionOrderId = $this->distributionOrderDb->add($linkUsers[$i], 
                $linkUsers[$i + 1], $data);

            $where = array(
                'shopOrderId'=>$shopOrder['shopOrderId']
            );

            $commoditys = $shopOrder['commodity'];
            foreach($commoditys as $commodity){
                $data = array(
                    'distributionOrderId' =>$distributionOrderId,
                    'shopOrderId'=>$shopOrder['shopOrderId'],
                    'shopCommodityId'=>$commodity['shopCommodityId'],
                    'price'=>0
                );
            	$this->distributionCommodityAo->add($data);
            }
        }
    }
}
