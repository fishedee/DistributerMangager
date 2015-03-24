<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderStaticAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('order/orderDb', 'orderDb');
    }

    public function getOrderStatic($useId, $where, $limit){
        $where['$userId'] = $userId;
        return $this->orderDb->search($where, $limit);
    }
}
