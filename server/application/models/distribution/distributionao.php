<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityAo extends CI_Model
{
    public function __contruct(){
        parent::__construct();
        $this->load->model('distribution/distributionDb', 'distributionDb');
    }

    private function check($upUserId, $downUserId){
        return 0;
    }

    public function add($upUserId, $downUserId, $state){
        $this->check($upUserId, $downUserId);
        $this->distributionDb->add($upUserId, $downUserId, $state); 
    }

    public function del($upUserId, $downUserId){
        $this->distributionDb->del($upUserId, $downUserId);
    }

    public function mod($distributionId, $state){
        $data = array(
            'state'=>$state
        );

        $this->distributionDb->mod($distributionId, $data);
    }

    public function get($upUserId, $downUserId){
        return $this->distributionDb->get($upUserId, $downUserId); 
    }

    private $path = array();
    private $restult_path = array();
    
    private dfs($originUserId, $userId){
        if($originUserId == $userId){
            $result_path = $path;
            return;

        $upUserIds = $this->distributionDb->getUpUser($userId);
        foreach($upUserIds as $upUserId){
            $path[$upUserId] = 1;
            $this->dfs($upUserId);
            unset($path[$upUserId]);
        }
    }

    public function getLink($originUserId, $userId){
        $this->path = array(); 
        $this->dfs($originUserId, $userId);
        return $result_path;
    }
}

