<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityClassifyAO extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->model('classify/CommodityClassifyDb', 'commodityClassifyDb');
    }

    public function search($userId, $dataWhere, $dataLimit){
        $dataWhere['userId'] = $userId;
        return $this->commodityClassifyDb->search($dataWhere, $dataLimit);
    }

    public function get($shopCommodityClassifyId){
        $classify = $this->commodityClassifyDb->get($shopCommodityClassifyId);
        return $classify;
    }

    public function del($userId, $shopCommodityClassifyId){
        $classify = $this->commodityClassifyDb->get($shopCommodityClassifyId);
        if($classify['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无此权限操作');

        $this->commodityClassifyDb->del($shopCommodityClassifyId);

        //通知商品挂载的相关分类被删除了
        //TODO
    }

    public function add($userId, $data){
        $maxSort = $this->commodityClassifyDb->getMaxSortByUser($userId);
        $data['userId'] = $userId;
        if($maxSort == null)
            $data['sort'] = 1;
        else
            $data['sort'] = $maxSort + 1;
        $data['userId'] = $userId;
        $this->CommodityClassifyDb->add($data);
    }

    public function mod($userId, $shopCommodityClassifyId, $data){
        $classify = $this->commodityClassifyDb->get($shopCommodityClassifyId); 
        if($classify['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无此权限操作');

        $this->commodityClassifyDb->mod($shopCommodityClassifyId, $data);
    }
}
