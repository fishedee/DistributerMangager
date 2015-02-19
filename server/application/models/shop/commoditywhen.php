<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityWhen extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('shop/commodityDb', 'commodityDb');
    }

    public function whenShopCommodityClassifyIdDel($shopCommodityClassifyId){
        $this->commodityDb->modByShopCommodityClassifyId($shopCommodityClassifyId,array(
            'shopCommodityClassifyId'=>0
        ));
    }
}























