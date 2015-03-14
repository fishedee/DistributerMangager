<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityAo extends CI_Model
{
    public function __contruct(){
        parent::__construct();
        $this->load->model('distribution/distributionDb', 'distributionDb');
    }

    public function search($where, $limit){
        return $this->distributionDb->search($where, $limit);
    }

    private function check($upUserId, $downUserId){
        return 0;
    }

    public function get($userId, $distributionId){
        $distribution = $this->distributionDb->get($distribution);
        if($distribution['upUserId'] != $userId && $distribution['downUserId'] != $userId)
            throw new CI_MyException(1, "无权查询此非本用户的分成关系");
        else
            return $distribution;
    }
    public function add($upUserId, $downUserId, $state){
        $this->check($upUserId, $downUserId);
        $this->distributionDb->add($upUserId, $downUserId, $state); 
    }

    public function del($userId, $distributionId){
        $distribution = $this->get($userId, $distributionId);
        $this->distributionDb->del($distribution['distributionId']);
    }

    public function mod($distributionId, $state){
        $data = array(
            'state'=>$state
        );

        $this->distributionDb->mod($distributionId, $data);
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

