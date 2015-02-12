<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityClassifyAO extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->model('shop/CommodityClassifyDb', 'commodityClassifyDb');
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

        //修改下级分类变为一级分类
        $this->commodityClassifyDb->modByParent($shopCommodityClassifyId,array('parent'=>0));

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
        
        if($data['parent'] != 0){
            $parentClassify = $this->commodityClassifyDb->get($data['parent']);
            if($parentClassify['parent'] != 0)
                throw new CI_MyException(1, '上级分类是二级分类，不能再添加下级分类'); 
        }

        $data['userId'] = $userId;
        $this->commodityClassifyDb->add($data);
    }

    public function mod($userId, $shopCommodityClassifyId, $data){
        $classify = $this->commodityClassifyDb->get($shopCommodityClassifyId); 
        if($classify['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无此权限操作');

        $dataWhere = array('parent'=>$shopCommodityClassifyId);

        $ChildrenClassify = $this->commodityClassifyDb->search($dataWhere, array());
        $count = $ChildrenClassify['count'];
        if($count != 0 && $data['parent'] != 0)
            throw new CI_MyException(1, '当前分类有子分类，不能成为二级目录');

        if($data['parent'] != 0){
            $parentClassify = $this->commodityClassifyDb->get($data['parent']);
            if($parentClassify['parent'] != 0)
                throw new CI_MyException(1, '上级分类是二级分类，不能再添加下级分类'); 
        }
        $this->commodityClassifyDb->mod($shopCommodityClassifyId, $data);
    }

    public function move($userId, $shopCommodityClassifyId, $direction){
        //取出所有分类
        $dataWhere['userId'] = $userId;
        $allClassify = $this->commodityClassifyDb->search($dataWhere, array());
        $allClassify = $allClassify['data'];

        //计算上一级分类，与下一级分类
        $index = -1;
        foreach( $allClassify as $key=>$singleClassify){
            if($singleClassify['shopCommodityClassifyId'] == $shopCommodityClassifyId){
                $index = $key;
                break;
            }
        }
        if($index == -1)
            throw new CI_MyException(1, '不存在此分类');
        $currentClassify = $allClassify[$index];

        //调整sort值
        if($direction == 'up'){
            if($index - 1 < 0)
                throw new CI_MyException(1, '不能再往上调整了');
            $prevClassify = $allClassify[$index - 1];
            $newCurrentSort = $prevClassify['sort']; 
            $newCurrentId = $currentClassify['shopCommodityClassifyId'];
            $newOtherSort = $currentClassify['sort'];
            $newOtherId = $prevClassify['shopCommodityClassifyId'];
        }else{
			if( $index + 1 >= count($allClassify) )
				throw new CI_MyException(1,'不能再下调整了');
            $nextClassify = $allClassify[$index + 1];
            $newCurrentSort = $nextClassify['sort'];
            $newCurrentId = $currentClassify['shopCommodityClassifyId'];
            $newOtherSort = $currentClassify['sort'];
            $newOtherId = $nextClassify['shopCommodityClassifyId'];
        }

        //更新数据库
        $this->commodityClassifyDb->mod($newOtherId, array('sort'=>$newOtherSort));
        $this->commodityClassifyDb->mod($newCurrentId, array('sort'=>$newCurrentSort));
    }
















}
