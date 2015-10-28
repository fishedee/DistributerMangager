<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishOrderAo extends CI_Model{

    private $start = array(
        'ON' => 1,
        'OFF'=> 0
        );

    private $state = array(
        'NO_ACCEPT'=> 0,
        'ACCEPT'   => 1,
        'CLOSE'    => 2,
        'CANCLE'   => 3,
        );

    public function __construct(){
        parent::__construct();
        $this->load->model('dish/dishOrderDb','dishOrderDb');
        $this->load->model('dish/dishAo','dishAo');
        $this->load->model('dish/dishOrderDetailDb','dishOrderDetailDb');
        $this->load->model('board/boardAo','boardAo');
        $this->load->model('board/boardDb','boardDb');
        $this->load->model('dish/dishOrderBoardTeamDb','dishOrderBoardTeamDb');
        $this->load->model('dish/dishOrderBoardDb','dishOrderBoardDb');
        $this->load->model('dish/dishOrderStateEnum','dishOrderStateEnum');
        $this->load->model('room/roomAo','roomAo');
        $this->load->model('board/boardDateAo','boardDateAo');
        $this->load->model('board/boardTypeAo','boardTypeAo');
    }

    //查看订餐订单
    public function search($userId,$dataWhere,$dataLimit,$type){
        $dataWhere['userId'] = $userId;
        $info = $this->dishOrderDb->search($dataWhere,$dataLimit,$type);
        foreach ($info['data'] as $key => $value) {
            $info['data'][$key]['price'] = sprintf("%.2f", $info['data'][$key]['price']/100);
        }
        return $info;
    }

    //查询就餐中的订单  
    // public function searchNow($userId,$dataWhere,$dataLimit){
    //     if(!$dataWhere['boardId']){
    //         throw new CI_MyException(1,'请选择餐桌id');
    //     }
    //     $orderNoInfo = $this->dishOrderBoardDb->getOrderNoInfo($dataWhere['boardId']);
    //     $arr = array();
    //     foreach ($orderNoInfo as $key => $value) {
    //         $arr[] = $value['orderNo'];
    //     }
    //     $info = $this->dishOrderDb->searchNow($arr,$dataLimit);
    //     foreach ($info['data'] as $key => $value) {
    //         $info['data'][$key]['price'] = sprintf("%.2f", $info['data'][$key]['price']/100);
    //     }
    //     return $info;
    // }

    //查询餐桌订单状况
    public function searchBoard($userId,$dataWhere,$dataLimit){
        if(!$dataWhere['boardId']){
            throw new CI_MyException(1,'请选择餐桌id');
        }
        $dataWhere['userId'] = $userId;
        return $this->dishOrderDb->searchBoard($dataWhere,$dataLimit);
    }

    //订单编号唯一性
    public function checkOrderNo($orderNo){
    	return $this->dishOrderDb->checkOrderNo($orderNo);
    }

    //插入订单
    public function addOrderOnce($data){
    	$orderNo = '';
        $userId = $data['userId'];
        $clientId = $data['clientId'];
    	while (1) {
    		$orderNo = time().$userId.$clientId.mt_rand(1,9);
	    	$result  = $this->checkOrderNo($orderNo);
	    	if(!$result){
	    		throw new CI_MyException(1,'订单号重复');
	    	}else{
	    		break;
	    	}
    	}
        $data['orderNo'] = $orderNo;
        $data['createTime'] = date('Y-m-d H:i:s',time());
        $data['modifyTime'] = date('Y-m-d H:i:s',time());
    	return $this->dishOrderDb->addOrderOnce($data);
    }

    //我的订单
    public function myOrder($userId,$clientId){
        $orderInfo = $this->dishOrderDb->myOrder($userId,$clientId);
        foreach ($orderInfo as $key => $value) {
            $orderInfo[$key]['price'] = sprintf('%.2f',$value['price']/100);
            if($value['boardId']){
                $boardInfo = $this->boardAo->getBoardInfo($userId,$value['boardId']);
                $orderInfo[$key]['boardNum'] = $boardInfo['boardNum'];
            }
        }
        $roomInfo = $this->roomAo->getRoomInfo($userId);
        return array(
            'orderInfo'=>$orderInfo,
            'roomName' =>$roomInfo['roomName']
            );
    }

    //我的订单详情
    public function myOrderDetail($userId,$clientId,$orderNo){
        $orderInfo = $this->getOrderInfo($userId,$orderNo);
        $orderDetail = $this->dishOrderDetailDb->getOrderDetailInfo($orderInfo['orderNo']);
        foreach ($orderDetail as $key => $value) {
            $orderDetail[$key]['dishPrice'] = sprintf('%.2f',$value['dishPrice']/100);
            unset($orderDetail[$key]['detail']);
        }
        $room[] = 'roomName';
        $room[]  = 'roomPhone';
        $room[] = 'roomAddress';
        $roomInfo = $this->roomAo->getRoomInfo($userId,$room);
        $orderInfo['orderDetail'] = $orderDetail;
        $orderInfo['price'] = sprintf('%.2f',$orderInfo['price']);
        $orderInfo['roomName'] = $roomInfo['roomName'];
        $orderInfo['roomPhone']= $roomInfo['roomPhone'];
        $orderInfo['roomAddress'] = $roomInfo['roomAddress'];
        $orderInfo['roomInfo'] = $roomInfo;
        unset($orderInfo['orderDetailInfo']);
        return $orderInfo;
    }

    //下单
    public function placeOrder($userId,$clientId,$data,$pay,$remark,$type,$code='',$name='',$phone='',$address='',$delivery=''){
    	$arr = array();
    	$sum = 0;
        $boardId = 0;

        if($phone){
            if( preg_match('/^[0-9]{11}$/',$phone) == 0 )
                throw new CI_MyException(1,'请输入11位的手机号码');
        }

    	if($type == 1){
            //检查前端数据的合法性
            foreach ($data as $key => $value) {
                if($value['num'] <= 0 ){
                    throw new CI_MyException(1,'下单数量非法');
                }
                if($type == 1 && !$value['boardId']){
                    throw new CI_MyException(1,'餐桌id不能为空');
                }
                $dishInfo = $this->dishAo->getDish($userId,$value['dishId']);
                if($dishInfo){
                    $arr[] = $dishInfo;
                }
                $sum += $dishInfo['dishPrice'] * 100 * $value['num'];
                $boardId = $value['boardId'];
            }
            //插入订单
            $arr = array();
            $arr['userId']  = $userId;
            $arr['clientId']= $clientId;
            $arr['price']   = $sum;
            $arr['pay']     = $pay;
            $arr['boardId'] = $boardId;
            $arr['remark']  = $remark;
            $arr['type']    = $type;
            $orderNo = $this->addOrderOnce($arr);
            if(!$orderNo){
                throw new CI_MyException(1,'订单插入失败');
            }

            foreach ($data as $key => $value) {
                $data[$key]['orderNo'] = $orderNo;
                unset($data[$key]['boardId']);
            }
            //插入订单明细
            $result = $this->dishOrderDetailDb->addOrderDetail($data);
            if($result != count($data)){
                throw new CI_MyException(1,'订单明细插入失败');
            }

            //Edward
            //print_r(json_encode($this->search($userId,array('orderNo'=>$orderNo),array('pageIndex'=>0,'pageSize'=>1))['data'][0]));die();

            if($pay == 1){  
                //需要在线支付
                $orderInfo = $this->DishOrderAo->getOrderInfo($orderNo);
                //微信统一下单
                $wxPrePayId= $this->dishOrderDb->addWxOrder($userId,$clientId,$orderInfo);
            }else{

                //Edward
                //发送websockit订单消息给商家
                $client = stream_socket_client('tcp://0.0.0.0:9999');
                //if(!$client){throw new CI_MyException(1,'websockit连接失败');}
                $orderData = json_encode($this->search($userId,array('orderNo'=>$orderNo),array('pageIndex'=>0,'pageSize'=>1),$type)['data'][0]);
                fwrite($client,$orderData."\n");


                //不需要在线支付 即目前已经完成下单工作 等待商家的确认
                return $orderNo;
            }
        }elseif($type == 2){
            //检测验证码
            // if($data['code'] != $this->session->userdata('booking_phone_code')){
                // throw new CI_MyException(1,'验证码错误');
            // }
            if($data['code'] != '1111'){
                throw new CI_MyException(1,'验证码错误');
            }
            if($userId != $data['userId']){
                throw new CI_MyException(1,'用户id非法');
            }
            //查询时间
            $time = $this->boardDateAo->getDate($userId,$data['confirmTime'])['time'];
            $bookingTime = $data['confirmDay'].' '.$time;
            if(strtotime($bookingTime) < time()){
                throw new CI_MyException(1,'预定时间非法');
            }
            //查看类型
            $boardType = $this->boardTypeAo->getType($userId,$data['confirmType']);
            $arr['userId'] = $userId;
            $arr['clientId'] = $clientId;
            $arr['boardTypeId'] = $data['confirmType'];
            $arr['bookingTime'] = $bookingTime;
            $arr['people'] = $data['peopleNum'];
            $arr['remark'] = $remark;
            $arr['name']   = $data['name'];
            $arr['phone']  = $data['phone'];
            $arr['type']   = $type;
            $arr['name']   = $data['name'];
            $arr['phone']  = $data['phone'];
            //插入订单
            $orderNo = $this->addOrderOnce($arr);
            //插入提醒
            $this->load->model('board/bookingRemindDb','bookingRemindDb');
            $data = array();
            $data['userId'] = $userId;
            $data['clientId'] = $clientId;
            $data['phone'] = $arr['phone'];
            $data['type'] = $type;
            $this->bookingRemindDb->add($data);
            return $orderNo;
        }elseif($type == 3){
            if($code != '1111'){
                throw new CI_MyException(1,'验证码错误');
            }
            //检查前端数据的合法性
            foreach ($data as $key => $value) {
                if($value['num'] <= 0 ){
                    throw new CI_MyException(1,'下单数量非法');
                }
                $dishInfo = $this->dishAo->getDish($userId,$value['dishId']);
                if($dishInfo){
                    $arr[] = $dishInfo;
                }
                $sum += $dishInfo['dishPrice'] * 100 * $value['num'];
            }
            //插入订单
            $arr = array();
            $arr['userId']  = $userId;
            $arr['clientId']= $clientId;
            $arr['price']   = $sum;
            $arr['pay']     = $pay;
            $arr['boardId'] = $boardId;
            $arr['remark']  = $remark;
            $arr['type']    = $type;
            $arr['name']    = $name;
            $arr['phone']   = $phone;
            $arr['address'] = $address;
            $arr['delivery']= $delivery;
            $orderNo = $this->addOrderOnce($arr);
            if(!$orderNo){
                throw new CI_MyException(1,'订单插入失败');
            }

            foreach ($data as $key => $value) {
                $data[$key]['orderNo'] = $orderNo;
                unset($data[$key]['boardId']);
            }
            //插入订单明细
            $result = $this->dishOrderDetailDb->addOrderDetail($data);
            if($result != count($data)){
                throw new CI_MyException(1,'订单明细插入失败');
            }
            return $orderNo;
        }
    }

    //查询订单详情
    public function getOrderInfo($userId,$orderNo){
        $orderInfo = $this->dishOrderDb->getOrderInfo($orderNo);
        $orderInfo['price'] = sprintf('%.2f',($orderInfo['price']/100));
        if($orderInfo['userId'] != $userId){
            throw new CI_MyException(1,'无权查看');
        }
        $orderDetailInfo = $this->dishOrderDetailDb->getOrderDetailInfo($orderNo);
        foreach ($orderDetailInfo as $key => $value) {
            $dishInfo = $this->dishAo->getDish($userId,$value['dishId']);
            $orderDetailInfo[$key]['icon'] = $dishInfo['icon'];
            $orderDetailInfo[$key]['dishName'] = $dishInfo['dishName'];
            $orderDetailInfo[$key]['dishPrice']= sprintf("%.2f", $dishInfo['dishPrice']);
            $orderDetailInfo[$key]['dishOption'] = $value['dishOption'];
            $orderDetailInfo[$key]['remark'] = $value['remark'];
        }
        $orderInfo['orderDetailInfo'] = $orderDetailInfo;
        return $orderInfo;
    }

    //修改订单
    public function mod($orderNo,$data){
        $data['modifyTime'] = date('Y-m-d H:i:s',time());
        return $this->dishOrderDb->mod($orderNo,$data);
    }

    //商家受理订单
    public function accept($userId,$orderNo){
        $orderInfo = $this->dishOrderDb->getOrderInfo($orderNo);
        //判断订单的状态
        if($orderInfo['userId'] != $userId){
            throw new CI_MyException(1,'无权查看');
        }
        //判断订单的状态
        if($orderInfo['state'] != 0){
            throw new CI_MyException(1,'订单不处于可受理状态');
        }
        $data['state'] = $this->state['ACCEPT'];

        if($orderInfo['type'] == 1){
            //获取餐桌原来的状态
            $boardInfo = $this->boardAo->getBoardInfo($userId,$orderInfo['boardId']);
            $boardStart= $boardInfo['start'];
            $result = $this->mod($orderNo,$data);
            if($result){
                //餐桌开启状态
                $result = $this->boardDb->mod($userId,$orderInfo['boardId'],array('start'=>$this->start['ON']));
                if($result){
                    if($boardStart){
                        //早已经开启 -- 获取team值
                        $team = $this->dishOrderBoardTeamDb->getTeam($orderInfo['boardId']);
                    }else{

                        //开启餐桌
                        $data = array();
                        $data['start'] = $this->start['ON'];
                        $this->boardAo->mod($userId,$orderInfo['boardId'],$data);
                        //没开启 -- 记录消费记录
                        $data = array();
                        $data['boardId'] = $orderInfo['boardId'];
                        $data['createTime'] = date('Y-m-d H:i:s',time());
                        $this->dishOrderBoardTeamDb->addTeam($data);
                        $team = $this->dishOrderBoardTeamDb->getTeam($orderInfo['boardId']);
                    }
                    $data = array();
                    $data['orderNo'] = $orderNo;
                    $data['boardId'] = $orderInfo['boardId'];
                    $data['team']    = $team;
                    $data['createTime'] = date('Y-m-d H:i:s',time());
                    $data['modifyTime'] = date('Y-m-d H:i:s',time());
                    return $this->dishOrderBoardDb->addDishOrderBoard($data);
                }else{
                    throw new CI_MyException(1,'开启餐桌状态失败');
                }
            }else{
                throw new CI_MyException(1,'订单受理失败');
            }
        }elseif($orderInfo['type'] == 2){
            $result = $this->dishOrderDb->mod($orderNo,$data);
            if($result){
                return $result;
            }else{
                throw new CI_MyException(1,'预约受理失败');
            }
        }elseif($orderInfo['type'] == 3){
            $result = $this->mod($orderNo,$data);
            if($result){
                return $result;
            }else{
                throw new CI_MyException(1,'受理外卖失败');
            }
        }
    }

    //商家取消订单
    public function cancle($userId,$orderNo,$clientId){
        $orderInfo = $this->getOrderInfo($userId,$orderNo);
        if($orderInfo['type'] == 1){
            if($orderInfo['userId'] != $userId){
                throw new CI_MyException(1,'无效操作');
            }
            if($orderInfo['state'] != 0 && $orderInfo['state'] != 1){
                throw new CI_MyException(1,'订单不处于可取消状态');
            }
            if($clientId){
                if($orderInfo['state'] == 0){
                    $data['state'] = 3;
                    return $this->dishOrderDb->mod($orderNo,$data);
                }else{
                    throw new CI_MyException(1,'商家已经受理订单,请联系商家取消');
                }
            }else{
                if($orderInfo['state'] == 0){
                    $data['state'] = 3;
                    return $this->dishOrderDb->mod($orderNo,$data);
                }else{
                    return 0;
                }
            }
        }elseif($orderInfo['type'] == 2){
            if($orderInfo['state'] != $this->state['NO_ACCEPT']){
                throw new CI_MyException(1,'该预约不处于可取消状态');
            }
            if($this->session->userdata('userId')){
                return 'reason';
            }else{
                $data['state'] = 3;
                return $this->dishOrderDb->mod($orderNo,$data);
            }
        }elseif($orderInfo['type'] == 3){
            if($orderInfo['state'] != $this->state['NO_ACCEPT']){
                throw new CI_MyException(1,'该预约不处于可取消状态');
            }
            $data['state'] = 3;
            return $this->dishOrderDb->mod($orderNo,$data);
        }
    }

    //取消预约
    public function forbid($userId,$orderNo,$data){
        $orderInfo = $this->getOrderInfo($userId,$orderNo);
        if($orderInfo['state'] != $this->state['NO_ACCEPT']){
            throw new CI_MyException(1,'该预约不处于可取消状态');
        }
        $arr['state'] = $this->state['CANCLE'];
        $arr['reason']  = $data['data']['reason'];
        return $this->dishOrderDb->mod($orderNo,$arr);
    }

    //商家确实取消订单
    public function realyCancle($userId,$orderNo){
        $orderInfo = $this->getOrderInfo($userId,$orderNo);
        if($orderInfo['userId'] != $userId){
            throw new CI_MyException(1,'无效操作');
        }
        if($orderInfo['state'] != 0 && $orderInfo['state'] != 1){
            throw new CI_MyException(1,'订单不处于可取消状态');
        }
        $data['state'] = 3;
        return $this->dishOrderDb->mod($orderNo,$data);
    }

    //计算总价
    public function cal($userId,$dataWhere,$dataLimit,$states){
        if(!$dataWhere['boardId']){
            throw new CI_MyException(1,'请选择餐桌id');
        }
        $dataWhere['userId'] = $userId;
        return $this->dishOrderDb->cal($dataWhere,$dataLimit,$states);
    }

    //结账
    public function closeAccounts($userId,$boardId){
        $orderInfo = $this->dishOrderAo->getBoardOrderInfo($userId,$boardId);
        $sum = 0;
        foreach ($orderInfo as $key => $value) {
            if($value['state'] == $this->state['NO_ACCEPT']){
                throw new CI_MyException(1,'该餐桌还有订单处于未受理状态');
            }
            if($value['state'] == $this->state['ACCEPT']){
                $sum += $value['price'];
            }
        }
        $boardInfo = $this->boardAo->getBoardInfo($userId,$boardId);
        if($boardInfo['start'] != 1){
            throw new CI_MyException(1,'该餐桌不处于开启状态');
        }
        $data['start'] = 0;
        $result = $this->boardAo->mod($userId,$boardId,$data);
        if($result){
            //关闭餐桌记录
            $result = $this->dishOrderBoardDb->mod($boardId,$data);
            if($result){
                //关闭订单
                $result = $this->dishOrderDb->closeAccounts($userId,$boardId);
                if($result){
                    //记录
                    $this->load->model('dish/dishBoardCloseDb','dishBoardCloseDb');
                    $data = array();
                    $data['userId'] = $userId;
                    $data['boardId']= $boardId;
                    $data['price']  = $sum;
                    $this->dishBoardCloseDb->add($data);
                    return $result;
                }else{
                    throw new CI_MyException(1,'订单结账失败');
                }
            }else{
                throw new CI_MyException(1,'关闭餐桌记录失败');
            }
        }else{
            throw new CI_MyException(1,'关闭餐桌失败');
        }
    }

    //获取该张桌子的订单信息
    public function getBoardOrderInfo($userId,$boardId){
        return $this->dishOrderDb->getBoardOrderInfo($userId,$boardId);
    }  

    
}
