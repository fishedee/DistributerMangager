<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->library('argv','','argv');
        $this->load->model('shop/commodityDb', 'commodityDb');
        $this->load->model('shop/commodityUserAppDb', 'commodityUserAppDb');
        $this->load->model('shop/commodityStateEnum', 'commodityStateEnum');
        $this->load->model('user/userAppAo', 'userAppAo');
    }
    
    public function getFixedPrice($price){
        return sprintf("%.2f", $price/100);
    }

    private function findOriginCommodity($shopCommodity){
        $originCommodity = $shopCommodity;
        while($originCommodity['isLink'] == 1)
            $originCommodity = $this->commodityDb->get($originCommodity['shopLinkCommodityId']);
            
        $originCommodity['originShopCommodityId'] = $originCommodity['shopCommodityId'];
        $originCommodity['originUserId'] = $originCommodity['userId'];
        $originCommodity['originUserAppName'] = $this->userAppAo->get($originCommodity['originUserId'])['appName'];
        $originCommodity['isLink'] = $shopCommodity['isLink'];
        $originCommodity['shopCommodityId'] = $shopCommodity['shopCommodityId'];
        $originCommodity['userId'] = $shopCommodity['userId'];
        $originCommodity['appName'] = $this->userAppAo->get($originCommodity['userId'])['appName'];
        $originCommodity['shopCommodityClassifyId'] = $shopCommodity['shopCommodityClassifyId'];
        $originCommodity['shopLinkCommodityId'] = $shopCommodity['shopLinkCommodityId'];
        $originCommodity['priceShow'] = $this->getFixedPrice($originCommodity['price']);
        $originCommodity['oldPriceShow'] = $this->getFixedPrice($originCommodity['oldPrice']);

        return $originCommodity;
    }

    public function search($userId,$dataWhere, $dataLimit){
        $this->userAppAo->checkByUserId($userId);
        
        $dataWhere['userId'] = $userId;
        $data = $this->commodityDb->search($dataWhere, $dataLimit);

        foreach($data['data'] as $key=>$value)
            $data['data'][$key] = $this->findOriginCommodity($value);

        return $data;
    }

    public function searchAll($dataWhere, $dataLimit){
        $data = $this->commodityUserAppDb->search($dataWhere, $dataLimit);

        foreach($data['data'] as $key=>$value)
            $data['data'][$key] = $this->findOriginCommodity($value);

        return $data;
    }

    public function check($shopCommodity){
        if($shopCommodity['title'] == '' )
            throw new CI_MyException(1,'商品标题不能为空');
        if($shopCommodity['price'] <= 0 )
            throw new CI_MyException(1,'价格不能少于或等于0');
        if($shopCommodity['oldPrice'] <= 0 )
            throw new CI_MyException(1,'原价格不能少于或等于0');
    }

    public function checkLink($shopCommodityId){
        $this->commodityDb->get($shopCommodityId);
    }

    public function get($userId,$shopCommodityId){
        $shopCommodity = $this->commodityDb->get($shopCommodityId);
        if( $shopCommodity['userId'] != $userId)
            throw new CI_MyException(1,'非本商城用户无此权限');

        $originCommodity = $this->findOriginCommodity($shopCommodity);
        return $originCommodity;
    }

    public function getByOnlyId($shopCommodityId){
        $shopCommodity = $this->commodityDb->get($shopCommodityId);

        $originCommodity = $this->findOriginCommodity($shopCommodity);
        return $originCommodity;
    }

    public function getByIds($userId,$shopCommodityId){
        $data = $this->search($userId,array('shopCommodityId'=>$shopCommodityId),array())['data'];
        $map = array();
        foreach($data as $singleData){
            $map[$singleData['shopCommodityId']] = $singleData;
        }

        return $map;
    }

    public function getOnStoreByClassify($userId,$shopCommodityClassifyId){
        return $this->search($userId,array(
            'shopCommodityClassifyId'=>$shopCommodityClassifyId,
            'state'=>$this->commodityStateEnum->ON_STORAGE
            ),
            array()
        )['data'];
        return $data;
    }
    
    private function dfsDelCommodity($shopCommodityId){
        $links = $this->commodityDb->getByShopLinkCommodityId($shopCommodityId);
        $this->commodityDb->del($shopCommodityId);
        foreach($links as $link)
            $this->dfsDelCommodity($link['shopCommodityId']);
    }

    public function del($userId, $shopCommodityId){
        $shopCommodity = $this->commodityDb->get($shopCommodityId);
        if($shopCommodity['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无此权限');

        //$this->commodityDb->del($shopCommodityId);
        $this->dfsDelCommodity($shopCommodityId);
    }

    public function add($userId, $data){
        $data['price'] = $data['priceShow']*100;
        $data['oldPrice'] = $data['oldPriceShow']*100;
        $data['userId'] = $userId;
        $data['shopLinkCommodityId'] = 0;
        $data['isLink'] = 0;
        unset($data['priceShow']);
        unset($data['oldPriceShow']);
        $this->check($data);
        $this->commodityDb->add($data);
    }

    public function addLink($userId, $shopLinkCommodityId, $shopCommodityClassifyId){

        $this->checkLink(0,$shopLinkCommodityId);

        $data = array(
            'isLink'=>1,
            'shopLinkCommodityId'=>$shopLinkCommodityId,
            'userId'=>$userId,
            'shopCommodityClassifyId'=>$shopCommodityClassifyId,
        );
        
        $this->commodityDb->add($data); 
    }

    public function modLink($userId, $shopCommodityId, $shopLinkCommodityId,
                            $shopCommodityClassifyId){
        $shopCommodity = $this->commodityDb->get($shopCommodityId);    
        if($shopCommodity['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无权限操作');
        if($shopCommodity['isLink'] != 1)
            throw new CI_MyException(1, '此商品不是导入商品');
        if($shopCommodityId == $shopLinkCommodityId)
            throw new CI_MyException(1, '不能导入自己的商品');
        
        $this->checkLink($shopLinkCommodityId);
         
        $data = array(
            'shopLinkCommodityId'=>$shopLinkCommodityId,
            'shopCommodityClassifyId'=>$shopCommodityClassifyId,
        );
        $this->commodityDb->mod($shopCommodityId, $data);
    }
     
    public function mod($userId, $shopCommodityId, $data){
        $data['price'] = $data['priceShow']*100;
        $data['oldPrice'] = $data['oldPriceShow']*100;
        unset($data['priceShow']);
        unset($data['oldPriceShow']);
        $this->check($data);
        $shopCommodity = $this->commodityDb->get($shopCommodityId);
        if($shopCommodity['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无权限操作');
        if($shopCommodity['isLink'] != 0)
            throw new CI_MyException(1, '导入商品不能修改');

        $this->commodityDb->mod($shopCommodityId, $data);
    }

    public function reduceStock($userId, $shopCommodityId, $quantity){
        $shopCommodity = $this->commodityDb->get($shopCommodityId);
        if($shopCommodity['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无此权限');
        if($shopCommodity['isLink'] != 0)
            throw new CI_MyException(1, '导入商品不能修改');

        $this->commodityDb->reduceStock($shopCommodityId, $quantity);
    }
}
