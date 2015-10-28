<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishCommentAo extends CI_Model{

    public function __construct(){
        parent::__construct();
        $this->load->model('dish/dishCommentDb','dishCommentDb');
        $this->load->model('dish/dishAo','dishAo');
        $this->load->model('dish/dishDb','dishDb');
        $this->load->model('dish/dishOrderAo','dishOrderAo');
        $this->load->model('dish/dishOrderDb','dishOrderDb');
        $this->load->model('dish/dishOrderDetailDb','dishOrderDetailDb');
    }

    //检测评论资格
    public function checkComment($userId,$clientId,$orderNo){
        $orderInfo = $this->dishOrderAo->getOrderInfo($userId,$orderNo);
        if($orderNo['clientId'] != $clientId){
            throw new CI_MyException(1,'无效用户');
        }
        if($orderNo['state'] != 4){
            throw new CI_MyException(1,'该订单不处于可评价阶段');
        }
        return true;
    }

    public function publish($userId,$clientId,$dishId,$content,$orderNo,$degree,$orderDetailId){
        if(!$dishId){                                                      
            throw new CI_MyException(1,'请输入菜单编号');
        }
        if(!$content){
            throw new CI_MyException(1,'请输入评价内容');
        }
        $shop_degree = $degree['dianpu'] ? $degree['dianpu'] : 1;
        $taste_degree= $degree['kouWei'] ? $degree['kouWei'] : 1;
        $attitude_degree = $degree['taiDu'] ? $degree['taiDu'] : 1;
        $degree = ($shop_degree + $taste_degree + $attitude_degree)/3;
        $dishInfo = $this->dishAo->getDish($userId,$dishId);
        $data['clientId'] = $clientId;
        $data['dishId'] = $dishId;
        $data['content']= $content;
        $data['degree'] = $degree;
        $data['createTime'] = date('Y-m-d',time());
        $data['shop_degree']= $shop_degree;
        $data['taste_degree'] = $taste_degree;
        $data['attitude_degree'] = $attitude_degree;
        $result = $this->dishCommentDb->publish($data);
        if($result){
            //发表成功 修改订单明细表
            $data = array();
            $data['comment'] = 1;
            $result = $this->dishOrderDetailDb->mod($orderDetailId,$data);
            if(!$result){
                throw new CI_MyException(1,'您已经评价过了');
            }
            $result = $this->dishOrderDetailDb->checkOrderDetail($orderNo);
            if($result){
                $dataWhere['dishId'] = $dishId;
                $limit = array();
                $commentInfo = $this->dishCommentDb->search($dataWhere,$limit);
                $degree = 0;
                $count  = count($commentInfo['data']);
                foreach ($commentInfo['data'] as $key => $value) {
                    $degree += $value['degree'];
                }
                $data = array();
                $data['degree'] = $degree/$count;
                return $this->dishDb->mod($dishId,$data);
            }else{
                //关闭订单
                $data = array();
                $data['state'] = 2;
                return $this->dishOrderDb->mod($orderNo,$data);
            }
        }else{
            throw new CI_MyException(1,'发表评论失败');
        }
    }

    //查看评论
    public function search($userId,$dataWhere,$dataLimit){
        $this->load->model('client/clientAo','clientAo');
        if(!$dataWhere['dishId']){
            throw new CI_MyException(1,'请选择菜品id');
        }
        $dishInfo = $this->dishAo->getDish($userId,$dataWhere['dishId']);
        $result = $this->dishCommentDb->search($dataWhere,$dataLimit);
        foreach ($result['data'] as $key => $value) {
            $clientInfo = $this->clientAo->get($dishInfo['userId'],$value['clientId']);
            $result['data'][$key]['nickName'] = base64_decode($clientInfo['nickName']);
            $result['data'][$key]['headImgUrl'] = $clientInfo['headImgUrl'];
        }
        $result['degree'] = $dishInfo['degree'];
        return $result;
    }
}
