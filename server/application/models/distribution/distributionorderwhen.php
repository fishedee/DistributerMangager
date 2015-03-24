<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionOrderWhen extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionOrderStateEnum', 'distributionOrderStateEnum');
        $this->load->model('distribution/distributionOrderDb', 'distributionOrderDb');
        $this->load->model('distribution/distributionCommodityAo', 'distributionCommodityAo');
        $this->load->model('distribution/distributionAo', 'distributionAo');
    }

    public function whenGenerateOrder($entranceUserId, $shopOrder){
        $userId = $shopOrder['userId'];
        $linkUsers = $this->distributionAo->getLink($userId, $entranceUserId);

        for($i = 0; $i < count($linkUsers)-1; ++$i){
            //添加分成订单
            $distributionOrderId = $this->distributionOrderDb->add(
                $linkUsers[$i], 
                $linkUsers[$i + 1], 
                array(
                    'price'=>0,
                    'shopOrderId'=>$shopOrder['shopOrderId'],
                    'state'=>$this->distributionOrderStateEnum->UN_PAY
                )
            );

            //添加分成订单下的商品信息
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
