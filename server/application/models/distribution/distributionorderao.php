<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionOrderAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionorderDb', 'distributionOrderDb');
        $this->load->model('distribution/distributionAo', 'distributionAo');
    }

    public function getFixedPrice($price){
        return sprintf("%.2f", $price/100);
    }

    public function add($upUserId, $downUserId, $data){
        $where = array(
            'upUserId'=>$upUserId,
            'downUserId'=>$downUserId
        );

        $response = $this->distributionAo->search($where, array());
        if($response['count'] == 0)
            throw new CI_MyException(1, '用户间不存在分成关系');

        $this->distributionOrderDb->add($upUserId, $downUserId, $data);
    }

    public function mod($distributionOrderId, $data){
        $data['price'] = $data['priceShow']*100;   
        unset($data['priceShow']);
        if($data['price'] <= 0)
            throw new CI_MyException(1, '价格不能少于或等于0');       
        $this->distributionOrderDb->mod($distributionId, $data);
    }

    public function payOrder($distributionOrderId){
        $order = $this->distributionOrderDb->get($distributionOrderId);
        if($order['state'] != 0)
            throw new CI_MyException(1, '此分成不能执行付款操作');

        $data = array(
            'state'=>1
        );
        $this->mod($distributionOrderId, $data);
    }

    public function confirm($distributionOrderId){
        $order = $this->distributionOrderDb->get($distributionOrderId);
        if($order['state'] != 1)
            throw new CI_MyException(1, '此分成不能执行付款操作');

        $data = array(
            'state'=>2
        );
        $this->mod($distributionOrderId, $data);
    }
     
    public function del($distributionOrderId){
        $this->distributionOrderDb->del($distributionOrderId);
    }
}
