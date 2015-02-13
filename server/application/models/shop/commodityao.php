<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityAO extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('shop/CommodityDb', 'commodityDb');
    }

    public function search($dataWhere, $dataLimit){
        return $this->commodityDb->search($dataWhere, $dataLimit);
    }

    public function get($commodityId){
        $commodity = $this->commodityDb->get($commodityId);
        return $commodity;
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
        );
        return $this->commodityDb->search($dataWhere, $dataLimit);
    }

    public function move($userId, $shopCommodityId, $direction){
        //取出所有商品
        $dataWhere['userId'] = $userId;
        $allCommodity = $this->commodityDb->search($dataWhere, array());
        $allCommodity = $allCommodity['data'];

        //计算上一个商品，和下一个商品
        $index = -1;
        foreach($allCommodity as $key=>$singleCommodity){
            if($singleCommodity['shopCommodityId'] == $shopCommodityId){
                $index = $key;
                break;
            }
        }
        if($index == -1)
            throw new CI_MyException(1, '不存在此分类');
        $currentCommodity = $allCommodity[$index];

        //调整sort
        if($direction == 'up'){
            if($index - 1 < 0)
                throw new CI_MyException(1, '不能再往上调整');
            $prevCommodity = $allCommodity[$index - 1];
            $newCurrentSort = $prevCommodity['sort'];
            $newCurrentId = $currentCommodity['shopCommodityId'];
            $newOtherSort = $currentCommodity['sort'];
            $newOtherId = $prevCommodity['shopCommodityId'];
        }else{
            if($index + 1 >= count($allCommodity))
                throw new CI_MyException(1, '不能再往下调整');
            $nextCommodity = $allCommodity[$index + 1];
            $newCurrentSort = $nextCommodity['sort'];
            $newCurrentId = $currentCommodity['shopCommodityId'];
            $newOtherSort = $currentCommodity['sort'];
            $newOtherId = $nextCommodity['shopCommodityId'];
        }

        //更新数据库
        $this->commodityDb->mod($newOtherId, array('sort'=>$newOtherSort));
        $this->commodityDb->mod($newCurrentId, array('sort'=>$newCurrentSort));
    }
}























