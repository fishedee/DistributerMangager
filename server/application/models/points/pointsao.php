<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PointsAo extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->model('points/pointsDb','pointsDb');
        $this->load->model('client/clientAo','clientAo');
    }

    public function getFixedPrice($price){
        return sprintf("%.2f", $price);
    }

    public function checkData($data){
        if(!$data){
            throw new CI_MyException(1,'请检测输入参数');
        }
        foreach ($data as $key => $value) {
            if(!$value){
                throw new CI_MyException(1,'输入参数均不能为空');
            }
            if($key == 'price' || $key == 'num' || $key == 'points'){
                if(!is_numeric($value)){
                    throw new CI_MyException(1,'价格、库存、兑换积分均必须为数字');
                }
                if($value<=0){
                    throw new CI_MyException(1,'价格、库存、兑换积分均必须要大于0');
                }
            }
        }
        return $data;
    }

    //查看提现申请
    public function search($vender,$dataWhere,$dataLimit){
        $dataWhere['vender'] = $vender;
        $result = $this->pointsDb->search($dataWhere,$dataLimit);
        $data   = $result['data'];
        foreach ($data as $key => $value) {
            $data[$key]['price'] = $this->getFixedPrice($value['price']/100);
        }
        $result['data'] = $data;
        return $result;
    }

    //获取商品详情
    public function getProductInfo($vender,$productId){
        $productInfo = $this->pointsDb->getProductInfo($productId);
        if($productInfo){
            $productInfo = $productInfo[0];
            if($productInfo['vender'] != $vender){
                throw new CI_MyException(1,'无权查看');
            }
            $productInfo['price'] = $this->getFixedPrice($productInfo['price']/100);
            return $productInfo;
        }else{
            throw new CI_MyException(1,'无效商品id');
        }
    }

    //添加商品
    public function add($vender,$data){
        $data = $this->checkData($data);
        $data['vender'] = $vender;
        $data['price']  = $data['price'] * 100;
        $result = $this->pointsDb->add($data);
        if($result){
            return $result;
        }else{
            throw new CI_MyException(1,'增加商品失败');
        }
    }

    //编辑商品
    public function mod($vender,$productId,$data){
        $info = $this->getProductInfo($vender,$productId);
        $data = $this->checkData($data);
        $data['price'] = $data['price'] * 100;
        $result = $this->pointsDb->mod($productId,$data);
        if($result){
            return $result;
        }else{
            throw new CI_MyException(1,'更新失败');
        }
    }

    //更改上下架状态
    public function change($vender,$productId){
        $info = $this->getProductInfo($vender,$productId);
        if($info['state'] == 1){
            $data['state'] = 0;
        }else{
            $data['state'] = 1;
        }
        $result = $this->pointsDb->mod($productId,$data);
        if($result){
            return $result;
        }else{
            throw new CI_MyException(1,'更新失败');
        }
    }

    //前端获取商品
    public function fontGetProductInfo($userId){
        $info = $this->pointsDb->fontGetProductInfo($userId);
        foreach ($info as $key => $value) {
            $info[$key]['price'] = sprintf('%.2f',$value['price']/100);
        }
        return $info;
    }
}