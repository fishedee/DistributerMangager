<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionCommodity extends CI_Constroller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distributionCommodityAo', 'distributionCommodityAo');
        $this->load->model('user/loginAo', 'loginAo');
    }

    /**
     * @view json
     */
    public function add(){
        $data = $this->argv->checkPost(array(
            array('distributionId', 'require'),
            array('shopOrderId', 'require'),
            array('shopCommodityId', 'require'),
            array('price', 'require')
        );

        $this->distributionCommodityAo->add($data);  
    }

    /**
     * $view json
     */
    public function mod(){
        $data = $this->argv->checkPost(array(
            array('distributionCommodityId', 'require'),
            array('price', 'option'),
            array('distributionOrderId', 'option'),
            array('shopOrderId', 'option'),
            array('shopCommodityId', 'option')
        ));

        $distributionCommodityId = $data['distributionCommodityId'];
        unset($data['distributionCommodityId']);
        $this->distributionCommodityAo->mod($distributionCommodityId, $data);
    }

}

