<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionStateEnum extends CI_Model
{
    public $enums = array(
        array(1, 'ON_REQUEST', "请求关系"),
        array(2, 'ON_ACCEPT', '已建立关系')
    ));

    public function __construct(){
        parent::__construct();
        $this->load->library('enum','', 'enum');
        $this->enum->setEnum($this, $this->enums);
    }
};
