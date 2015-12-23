<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionPosterEnum extends CI_Model
{
    public $enums = array(
        array(1, 'MID', '二维码处于正中间'),
        array(2, 'BOTTOM_LEFT', '二维码处于下方偏左侧'),
    );

    public function __construct(){
        parent::__construct();
        $this->load->library('enum', '', 'enum');
        $this->enum->setEnum($this, $this->enums);
    }
};
