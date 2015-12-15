<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionOrderWhen extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionOrderStateEnum', 'distributionOrderStateEnum');
        $this->load->model('distribution/distributionOrderDb', 'distributionOrderDb');
        $this->load->model('distribution/distributionCommodityAo', 'distributionCommodityAo');
        $this->load->model('distribution/distributionAo', 'distributionAo');
        $this->load->model('distribution/distributionConfigAo','distributionConfigAo');
        $this->load->model('distribution/distributionConfigEnum','distributionConfigEnum');
        $this->load->model('user/userAo','userAo');
        $this->load->model('client/scoreAo','scoreAo');
    }

    public function whenGenerateOrder($entranceUserId, $shopOrder,$tt){
        if($tt){
            $userId = $tt;
        }else{
            $userId = $shopOrder['userId'];
        }
        // $linkUsers = $this->distributionAo->getLink($userId, $entranceUserId);
        $links = $this->distributionAo->getLinks2($userId,$entranceUserId);
        foreach ($links as $key => $value) {
        	//添加分成订单
        	$distributionOrderId = $this->distributionOrderDb->add(
        		$value['upUserId'],
        		$value['downUserId'],
        		array(
        			'price'=>$shopOrder['price'] * $value['distributionPercent'] * 0.01,
        			'shopOrderId'=>$shopOrder['shopOrderId'],
        			'state'=>$this->distributionOrderStateEnum->UN_PAY,
                    'vender'=>$userId,
                    'entranceUserId'=>$entranceUserId,
                    'distributionId'=>$value['distributionId']
        			)
        		);
        		//添加分成订单下的商品信息
        		$commoditys = $shopOrder['commodity'];
        		foreach ($commoditys as $commodity) {
        			$data = array(
        				'distributionOrderId'=>$distributionOrderId,
        				'shopOrderId'=>$shopOrder['shopOrderId'],
        				'shopCommodityId'=>$commodity['shopCommodityId'],
        				'price'=>$commodity['price'] * $commodity['quantity'] * $value['distributionPercent'] * 0.01
        				);
        			$this->distributionCommodityAo->add($data);
        		}
        }

        // for($i = 0; $i < count($linkUsers)-1; ++$i){
        //     //添加分成订单
        //     $distributionOrderId = $this->distributionOrderDb->add(
        //         $linkUsers[$i], 
        //         $linkUsers[$i + 1], 
        //         array(
        //             'price'=>0,
        //             'shopOrderId'=>$shopOrder['shopOrderId'],
        //             'state'=>$this->distributionOrderStateEnum->UN_PAY
        //         )
        //     );

        //     //添加分成订单下的商品信息
        //     $commoditys = $shopOrder['commodity'];
        //     foreach($commoditys as $commodity){
        //         $data = array(
        //             'distributionOrderId' =>$distributionOrderId,
        //             'shopOrderId'=>$shopOrder['shopOrderId'],
        //             'shopCommodityId'=>$commodity['shopCommodityId'],
        //             'price'=>0
        //         );
        //     	$this->distributionCommodityAo->add($data);
        //     }
        // }
    }

    /**
     * 特殊分销
     * date:2015.11.30
     */
    public function whenSpecialOrder($entranceUserId,$shopOrder){
        $userId = $shopOrder['userId']; //厂家
        $config = $this->distributionConfigAo->getConfig($userId); //获取分成配置
        if($config['distribution'] == 0 || $config['distribution'] == $this->distributionConfigEnum->COMMON){
            throw new CI_MyException(1,'改分销模式只适用于特殊分销');
        }
        $distributionUser = $this->distributionAo->getDistributionUser($userId,$entranceUserId);
        if($distributionUser['member'] == 1){
            //高级会员 添加分成订单
            $distributionOrderId = $this->distributionOrderDb->add(
                $distributionUser['upUserId'],
                $distributionUser['downUserId'],
                array(
                    'price'=>0,
                    'shopOrderId'=>$shopOrder['shopOrderId'],
                    'state'=>$this->distributionOrderStateEnum->UN_PAY,
                    'vender'=>$userId,
                    'entranceUserId'=>$entranceUserId,
                    'distributionId'=>$distributionUser['distributionId'],
                    )
                );
            //添加分成订单下的商品信息
            $commoditys = $shopOrder['commodity'];
            foreach ($commoditys as $commodity) {
                $data = array(
                    'distributionOrderId'=>$distributionOrderId,
                    'shopOrderId'=>$shopOrder['shopOrderId'],
                    'shopCommodityId'=>$commodity['shopCommodityId'],
                    'price'=>0
                    );
                $this->distributionCommodityAo->add($data);
            }
        }
    }
}
