<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionOrderStateEnum extends CI_Model
{
    public $enums = array(
        array(0, 'UN_PAY', '未付款'),
        array(1, 'IN_PAY', '付款中'),
        array(2, 'HAS_PAY', '已付款'),
        array(3, 'HAS_CONFIRM', '已收款')
    );

    public function __construct(){
        parent::__construct();
        $this->load->library('enum', '', 'enum');
        $this->enum->setEnum($this, $this->enums);
    }
};
