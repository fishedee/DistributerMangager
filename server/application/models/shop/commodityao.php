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

    private function recursiveRefreshCommodity(){
        //取出所有链接商品
        $linkCommodity = $this->commodityDb->search(array('isLink'=>1),array());
        if($linkCommodity['count'] == 0 )
            return;
        $linkCommodityMap = array();
        foreach($linkCommodity['data'] as $singleLinkCommodity ){
            $linkCommodityMap[$singleLinkCommodity['shopCommodityId']] = $singleLinkCommodity;
        }
        
        //计算所有源商品的信息
        $originCommodityIds = array();
        foreach($linkCommodityMap as $singleLinkCommodityId=>$singleLinkCommodity ){
            $shopLinkCommodityId = $singleLinkCommodity['shopLinkCommodityId'];
            if( isset($linkCommodityMap[$shopLinkCommodityId]) )
                continue;
            $originCommodityIds[] = $shopLinkCommodityId;
        }

        $originCommodity = $this->commodityDb->search(array('shopCommodityId'=>$originCommodityIds),array());
        if($originCommodity['count'] == 0 )
            return;
        $originCommodityMap = array();
        foreach($originCommodity['data'] as $singleOriginCommodity){
            $originCommodityMap[$singleOriginCommodity['shopCommodityId']] = $singleOriginCommodity;
        }

        //更新所有链接商品下的所有商品信息
        $newLinkCommodityMap = array();
        foreach($linkCommodityMap as $singleLinkCommodityId=>$singleLinkCommodity ){
            $tempShopCommodity = $singleLinkCommodity;
            while($tempShopCommodity != null && $tempShopCommodity['isLink'] == 1 ){
                $tempShopCommodityId = $tempShopCommodity['shopLinkCommodityId'];
                if( isset($linkCommodityMap[$tempShopCommodityId]))
                    $tempShopCommodity = $linkCommodityMap[$tempShopCommodityId];
                else if( isset($originCommodityMap[$tempShopCommodityId]))
                    $tempShopCommodity = $originCommodityMap[$tempShopCommodityId];
                else 
                    $tempShopCommodity = null;
                log_message('error',json_encode($tempShopCommodity));
            }
            if($tempShopCommodity == null )
                return;
            $newLinkCommodityMap[] = array(
                'shopCommodityId'=>$singleLinkCommodity['shopCommodityId'],
                'title'=> $tempShopCommodity['title'],
                'icon'=> $tempShopCommodity['icon'],
                'introduction'=> $tempShopCommodity['introduction'],
                'detail'=> $tempShopCommodity['detail'],
                'price'=> $tempShopCommodity['price'],
                'oldPrice'=> $tempShopCommodity['oldPrice'],
                'inventory'=> $tempShopCommodity['inventory'],
                'state'=> $tempShopCommodity['state'],
                'remark'=> $tempShopCommodity['remark'],
            ); 
        }
        
        if(count($newLinkCommodityMap) == 0 )
            return;
        $this->commodityDb->modBatch($newLinkCommodityMap);
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

    public function search($userId,$dataWhere, $dataLimit,$rank=''){
        $this->userAppAo->checkByUserId($userId);
        $dataWhere['userId'] = $userId;
        $data = $this->commodityDb->search($dataWhere, $dataLimit,$rank);

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

    public function searchByKeyword($keyword){
        return $this->searchAll(
            array(
                'isLink'=>0,
                'title'=>$keyword,
                'state'=>$this->commodityStateEnum->ON_STORAGE
            ),
            array()
        )['data'];
    }

    public function check($shopCommodity){
        if($shopCommodity['title'] == '' )
            throw new CI_MyException(1,'商品标题不能为空');
        if($shopCommodity['price'] <= 0 )
            throw new CI_MyException(1,'价格不能少于或等于0');
        if($shopCommodity['oldPrice'] <= 0 )
            throw new CI_MyException(1,'原价格不能少于或等于0');
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

    public function getOnStoreByClassify($userId,$shopCommodityClassifyId,$rank=''){
        // $rank = 'priceDown';
        return $this->search($userId,array(
            'shopCommodityClassifyId'=>$shopCommodityClassifyId,
            'state'=>$this->commodityStateEnum->ON_STORAGE,
            'rank' => $rank,
            ),
            array(),$rank
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

        $this->dfsDelCommodity($shopCommodityId);
    }

    public function getNormalCommodityNum($userId){
        $data = $this->search($userId,array(
            'isLink'=>0,
        ),array());
        return $data['count'];
    }

    public function add($userId, $data){
        $maxSort = $this->commodityDb->getMaxSortByUser($userId);
        if( $maxSort == null )
            $sort = 1;
        else
            $sort = $maxSort + 1;

        $data['sort'] = $sort;
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

    public function checkLink($userId,$shopCommodityId,$shopLinkCommodityId){
        $shopCommodity = $this->commodityDb->get($shopLinkCommodityId);
        if($shopCommodity['isLink'] == 1 )
            throw new CI_MyException(1,'只能导入普通商品，导入商品不能再次导入');
        
        $linkCommodityData = $this->commodityDb->search(
            array(
                'userId'=>$userId,
                'shopLinkCommodityId'=>$shopLinkCommodityId
            ),
            array()
        );
        if($linkCommodityData['count'] != 0 && $linkCommodityData['data'][0]['shopCommodityId'] != $shopCommodityId)
            throw new CI_MyException(1,'不能重复导入同一个商品');
    }

    public function addLink($userId, $shopLinkCommodityId, $shopCommodityClassifyId){
        $maxSort = $this->commodityDb->getMaxSortByUser($userId);
        if( $maxSort == null )
            $sort = 1;
        else
            $sort = $maxSort + 1;

        $this->checkLink($userId,0,$shopLinkCommodityId);

        $data = array(
            'sort'=>$sort,
            'isLink'=>1,
            'shopLinkCommodityId'=>$shopLinkCommodityId,
            'userId'=>$userId,
            'shopCommodityClassifyId'=>$shopCommodityClassifyId,
        );
        
        $this->commodityDb->add($data); 

        $this->recursiveRefreshCommodity();
    }

    public function modLink($userId, $shopCommodityId, $shopLinkCommodityId,
                            $shopCommodityClassifyId){
        $shopCommodity = $this->commodityDb->get($shopCommodityId);    
        if($shopCommodity['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无权限操作');
        if($shopCommodity['isLink'] != 1)
            throw new CI_MyException(1, '此商品不是导入商品');
        
        $this->checkLink($userId,$shopCommodityId,$shopLinkCommodityId);
         
        $data = array(
            'shopLinkCommodityId'=>$shopLinkCommodityId,
            'shopCommodityClassifyId'=>$shopCommodityClassifyId,
        );
        $this->commodityDb->mod($shopCommodityId, $data);

        $this->recursiveRefreshCommodity();
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

        $this->recursiveRefreshCommodity();
    }

    public function reduceStock($userId, $shopCommodityId, $quantity){
        $shopCommodity = $this->commodityDb->get($shopCommodityId);
        if($shopCommodity['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无此权限');
        if($shopCommodity['isLink'] != 0)
            throw new CI_MyException(1, '导入商品不能修改');

        $this->commodityDb->reduceStock($shopCommodityId, $quantity);
    }

    public function move($userId,$shopCommodityId,$direction){
        //取出所有的分类
        $dataWhere['userId'] = $userId;
        $allClassify = $this->commodityDb->search($dataWhere,array());
        $allClassify = $allClassify['data'];
        
        //计算上一级的banner，与下一级的banner
        $index = -1;
        foreach( $allClassify as $key=>$singleClassify){
            if( $singleClassify['shopCommodityId'] == $shopCommodityId){
                $index = $key;
                break;
            }
        }
        if( $index == -1 )
            throw new CI_MyException(1,'不存在此商品');
        $currentClassify = $allClassify[$index];
        
        //调整sort值
        if( $direction == 'up' ){
            if( $index - 1 < 0 )
                throw new CI_MyException(1,'不能再往上调整了');
            $prevClassify =  $allClassify[$index - 1];
            $newCurrentSort = $prevClassify['sort'];
            $newCurrentId = $currentClassify['shopCommodityId'];
            $newOtherSort = $currentClassify['sort'];
            $newOtherId = $prevClassify['shopCommodityId'];
        }else{
            if( $index + 1 >= count($allClassify) )
                throw new CI_MyException(1,'不能再下调整了');
            $nextClassify =  $allClassify[$index + 1];
            $newCurrentSort = $nextClassify['sort'];
            $newCurrentId = $currentClassify['shopCommodityId'];
            $newOtherSort = $currentClassify['sort'];
            $newOtherId = $nextClassify['shopCommodityId'];
        }
        
        //更新数据库
        $this->commodityDb->mod($newOtherId,array('sort'=>$newOtherSort));
        $this->commodityDb->mod($newCurrentId,array('sort'=>$newCurrentSort));
    }

    public function getHeaderInfo($userId){
        return $this->commodityDb->getHeaderInfo($userId);
    }

    public function mobileGet($userId,$type,$classifyId){
        $data = $this->commodityDb->mobileGet($userId,$type,$classifyId);
        foreach($data as $key=>$value)
            $data[$key] = $this->findOriginCommodity($value);
        return $data;
    }
}
