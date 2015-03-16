<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionCommodityAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionCommodityDb', 'distributionCommodityDb');
       // $this->load->model('shop/commodityAo', 'commodityAo');
    }

    public function check($data){
        if( isset($data['price']) )
            if($data['price']  < 0)
                throw new CI_MyException(1, "商品金额不能为零");     
    }

    public function add($data){
        $this->check($data);
        $data['price'] = $data['price'] * 100;
        return $this->distributionCommodityDb->add($data);
    }

    public function mod($distributionCommodityId, $data){
        $this->check($data);
        $this->db->mod($distributionCommodityId, $data);
    }


    public function get($distributionOrderId, $shopOrderId){
        $where = array(
            'distributionOrderId'=>$distributionOrderId,
            'shopOrderId'=>$shopOrderId
        );

        $response = $this->distributionCommodityDb->search(
            $where, array());
        $distributionCommodity = $response['data'];
        foreach($distributionCommodity as $key=>$value)
            $distributionCommodity[$key]['priceShow'] = $this->commodityAo->getFixedPrice($distributionCommodity[$key]['price']);

        return $distribuComodity;
    }
}
