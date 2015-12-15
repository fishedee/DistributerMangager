<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionConfigEnum extends CI_Model
{
    public $enums = array(
        array(0, 'COMMON', '普通分销'),
        array(1, 'SPECIAL', '特殊分销'),
    );

    public function __construct(){
        parent::__construct();
        $this->load->library('enum', '', 'enum');
        $this->enum->setEnum($this, $this->enums);
    }
};
