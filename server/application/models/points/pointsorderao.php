 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PointsOrderAo extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->model('points/pointsOrderDb','pointsOrderDb');
        $this->load->model('client/clientAo','clientAo');
        $this->load->model('points/pointsAo','pointsAo');
        $this->load->model('points/pointsDb','pointsDb');
        $this->load->model('client/scoreDb','scoreDb');
    }

    public function getFixedPrice($price){
        return sprintf("%.2f", $price);
    }

    //兑换奖品
    public function exchange($userId,$productId,$clientId){
        $clientInfo = $this->clientAo->get($userId,$clientId);
        $productInfo = $this->pointsAo->getProductInfo($userId,$productId);
        if($clientInfo['score'] < $productInfo['points']){
            throw new CI_MyException(1,'抱歉!您的积分不够');
        }
        if($productInfo['points'] < 1){
            throw new CI_MyException(1,'奖品的库存不足');
        }
        $data['productName'] = $productInfo['productName'];
        $data['productImg']  = $productInfo['productImg'];
        $data['vender']      = $userId;
        $data['clientId']    = $clientId;
        $data['points']      = $productInfo['points'];
        $data['createTime'] = date('Y-m-d H:i:s',time());
        $result = $this->pointsOrderDb->add($data);
        //更新库存
        $data = array();
        $data['num'] = $productInfo['num'] - 1;
        $data['exchange'] = $productInfo['exchange'] + 1;
        $this->pointsDb->mod($productId,$data);
        if($result){
            //扣除积分
            $data = array();
            $data['score'] = $clientInfo['score'] - $productInfo['points'];
            $result = $this->clientAo->mod($userId,$clientId,$data);
            if($result){
                //积分日志
                $data = array();
                $data['clientId'] = $clientId;
                $data['event']    = 6;
                $data['createTime'] = date('Y-m-d H:i:s',time());
                $data['remark']   = '兑换奖品';
                $data['score']    = $productInfo['points'];
                $data['dis']      = 0;
                // $data['createTime'] = date('Y-m-d H:i:s',time());
                return $this->scoreDb->checkIn($data);
                // return $result;
            }else{
                throw new CI_MyException(1,'您的积分变更失败');
            }
        }else{
            throw new CI_MyException(1,'兑换失败');
        }
    }

    //我的奖品
    public function myProduct($userId,$clientId){
        return $this->pointsOrderDb->myProduct($userId,$clientId);
    }

    //查看兑换记录
    public function search($vender,$dataWhere,$dataLimit){
        $dataWhere['vender'] = $vender;
        $result = $this->pointsOrderDb->search($dataWhere,$dataLimit);
        foreach ($result['data'] as $key => $value) {
            $clientInfo = $this->clientAo->get($vender,$value['clientId']);
            $result['data'][$key]['nickName'] = base64_decode($clientInfo['nickName']);
            $result['data'][$key]['headImgUrl'] = $clientInfo['headImgUrl'];
        }
        return $result;
    }

    public function getOrder($vender,$orderId){
        $info = $this->pointsOrderDb->getOrder($orderId);
        if($info){
            if($info['vender'] == $vender){
                return $info;
            }else{
                throw new CI_MyException(1,'无效查看');
            }
        }else{
            throw new CI_MyException(1,'无效兑换记录');
        }
    }

    public function accept($vender,$orderId){
        $info = $this->getOrder($vender,$orderId);
        if($info['state'] != 0){
            throw new CI_MyException(1,'该记录已经受理');
        }
        $data['state'] = 1;
        return $this->pointsOrderDb->mod($orderId,$data);
    }
}