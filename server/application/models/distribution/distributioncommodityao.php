<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionCommodityAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionCommodityDb', 'distributionCommodityDb');
    }

    public function getFixedPrice($price){
        return sprintf("%.2f", $price);
    }

    public function check($data){
        if( $data['price']  < 0)
            throw new CI_MyException(1, "商品金额不能少于零");     
    }

    public function add($data){
        //校验数据
        $this->check($data);

        //转换price
        $data['price'] = $data['price'] * 100;

        //db
        return $this->distributionCommodityDb->add($data);
    }

    public function mod($distributionOrderId,$shopCommodityId, $data){
        //校验数据
        $this->check($data);

        //转换price
        $data['price'] = $data['price'] * 100;

        //转换price
        $this->distributionCommodityDb->modByDistributionOrderAndCommodity(
            $distributionOrderId, 
            $shopCommodityId,
            $data
        );
    }


    public function get($distributionOrderId){
        //获取数据
        $response = $this->distributionCommodityDb->search(
            array(
                 'distributionOrderId'=>$distributionOrderId
            ), 
            array()
        );
        $distributionCommodity = $response['data'];

        //转换price
        foreach($distributionCommodity as $key=>$value)
            $distributionCommodity[$key]['price'] = $this->getFixedPrice($distributionCommodity[$key]['price']/100);

        return $distributionCommodity;
    }
}
