<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityAO extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('shop/CommodityDb', 'commodityDb');
    }

    public function search($userId, $dataWhere, $dataLimit){
        $dataWhere['userId'] = $userId;
        return $this->commodityDb->search($dataWhere, $dataLimit);
    }

    public function get($commodityId){
        $commodity = $this->commodityDb->get($commodityId);
        return $ccommodity;
    }

    public function del($userId, $commodityId){
        $commodity = $this->commodityDb->get($commodityId);
        if($commodity['userId'] != $userId)
            throw new CI_MyException(1, '本商城用户无此权限');

        $this->commodityDb->del($commodityId);
    }

    public function add($userId, $data){
        $maxSort = $this->commodityDb->getMaxSortByUser($userId);
        $data['userId'] = $userId;
        if($maxSort == null)
            $data['sort'] = 1;
        else
            $data['sort'] = $maxSort + 1;

        $data['userId'] = $userId;
        $this->commodityDb->add($data);
    }

    public function mod($userId, $commodityId, $data){
        $commodity = $this->commodityDb->get($commodityId);
        if($commodity['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无权限操作');

        $this->commodityDb->mod($commodityId, $data);
    }

    public function searchByClassify($userId, $commodityClassifyId, $dataLimit){
        $dataWhere = array(
            'userId'=>$userId,
            'commodityClassifyId'=>$commodityClassifyId
        )
        return $this->commodityDb->search($dataWhere, $dataLimit);
    }
}
