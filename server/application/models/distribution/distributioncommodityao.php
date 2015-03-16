<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionCommodityAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionCommodityDb', 'distributionCommodityDb');
    }

    public function check($data){
        if( isset($data['price']) )
            if($data['price']  < 0)
                throw new CI_MyException(1, "商品金额不能为零");     
    }

    public function add($data){
        $this->check($data);
        return $this->distributionCommodityDb->add($data);
    }

    public function mod($distributionCommodityId, $data){
        $this->check($data);
        $this->db->mod($distributionCommodityId, $data);
    }

    public function get($distributionCommodityId){
        return $this->distributionCommodityDb->get($distributionCommodityId);
    }

    public function search($where, $limit){
        return $this->distributionCommodityDb->search($where, $limit);
    }
}
