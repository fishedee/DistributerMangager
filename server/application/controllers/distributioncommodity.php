<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionCommodity extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionCommodityAo', 'distributionCommodityAo');
        $this->load->model('user/loginAo', 'loginAo');
	$this->load->library('argv', 'argv');
    }

    /**
     * @view json
     * @test
     */
    public function add(){
        $data = $this->argv->checkPost(array(
            array('distributionOrderId', 'require'),
            array('shopOrderId', 'require'),
            array('shopCommodityId', 'require'),
            array('price', 'require')
        ));

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

	$user = $this->loginAo->checkMustLogin();

        $distributionCommodityId = $data['distributionCommodityId'];
        unset($data['distributionCommodityId']);

        $this->distributionCommodityAo->mod($user['userId'], $distributionCommodityId, $data);
    }
}

