<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishOrder extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('argv','argv');
		$this->load->model('user/loginAo', 'loginAo');
		$this->load->model('client/clientLoginAo', 'clientLoginAo');
		$this->load->model('dish/dishOrderAo','dishOrderAo');
  	}

  	/**
  	 * @view json
  	 * 订餐订单查询
  	 */
  	public function search(){
  		if($this->input->is_ajax_request()){
  			//检查输入参数    
			$dataWhere = $this->argv->checkGet(array(
				array('boardId','option'),
				array('state','option'),
                array('boardTypeId','option'),
                array('type','option'),
			));

			$dataLimit = $this->argv->checkGet(array(
				array('pageIndex','require'),
				array('pageSize','require'),
			));
			//检查权限
			$user = $this->loginAo->checkOrder(
	            $this->userPermissionEnum->ORDER_DINNER
	        );
	        $userId = $user['userId'];
$type = $dataWhere['type'];
	        return $this->dishOrderAo->search($userId,$dataWhere,$dataLimit,$type);
  		}
  	}

    /**
     * @view json
     * 查看点餐订单
     */
    public function searchOrder(){
        if($this->input->is_ajax_request()){
            //检查输入参数    
            $dataWhere = $this->argv->checkGet(array(
                array('boardId','option'),
                array('state','option'),
                array('boardTypeId','option'),
                array('type','option'),
            ));

            $dataLimit = $this->argv->checkGet(array(
                array('pageIndex','require'),
                array('pageSize','require'),
            ));
            $type = 1;
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            return $this->dishOrderAo->search($userId,$dataWhere,$dataLimit,$type);
        }
    }

    /**
     * @view json
     * 查询预约订单
     */
    public function searchBooking(){
        if($this->input->is_ajax_request()){
            //检查输入参数    
            $dataWhere = $this->argv->checkGet(array(
                array('boardId','option'),
                array('state','option'),
                array('boardTypeId','option'),
            ));

            $dataLimit = $this->argv->checkGet(array(
                array('pageIndex','require'),
                array('pageSize','require'),
            ));
            $type = 2;
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            return $this->dishOrderAo->search($userId,$dataWhere,$dataLimit,$type);
        }
    }

    /**
     * @view json
     * 查看外卖订单
     */
    public function searchTake(){
        if($this->input->is_ajax_request()){
            //检查输入参数    
            $dataWhere = $this->argv->checkGet(array(
                array('boardId','option'),
                array('state','option'),
                array('boardTypeId','option'),
            ));

            $dataLimit = $this->argv->checkGet(array(
                array('pageIndex','require'),
                array('pageSize','require'),
            ));
            $type = 3;
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            return $this->dishOrderAo->search($userId,$dataWhere,$dataLimit,$type);
        }
    }

    /**
     * @view json
     * 查看就餐中的订单
     */
    public function searchNow(){
        if($this->input->is_ajax_request()){
            //检查输入参数    
            $dataWhere = $this->argv->checkGet(array(
                array('states','option'),
            ));

            $dataLimit = $this->argv->checkGet(array(
                array('pageIndex','require'),
                array('pageSize','require'),
            ));

            $boardId = $this->input->get('boardId') ? $this->input->get('boardId') : '';
            $dataWhere['boardId'] = $boardId;
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            // return $this->dishOrderAo->searchNow($userId,$dataWhere,$dataLimit);
            $info = $this->dishOrderAo->searchBoard($userId,$dataWhere,$dataLimit);
            return $info;
        }
    }

  	/**
  	 * @view json
  	 * 下单
  	 */
  	public function placeOrder(){
  		//检查输入参数    
		$user = $this->argv->checkGet(array(
			array('userId','option'),
		));
		$userId = $user['userId'];

		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($userId);
		$clientId = $client['clientId'];

        $type = $this->input->post('type') ? $this->input->post('type') : 1;
        if($type == 1){
            $datas = $this->input->post();
            $data  = array();
            if(!isset($datas['cart'])){
                throw new CI_MyException(1,'请返回选择菜品');
            }
            foreach ($datas['cart'] as $key => $value) {
                $arr = array();
                $arr['dishId'] = $key;
                $arr['num']    = $value['num'];
                $arr['boardId']= $this->input->post('boardId');
                $arr['remark'] = $value['remark'];
                $data[] = $arr;
            }
        }elseif($type == 2){
            $data = $this->input->post();
        }elseif($type == 3){
            $datas = $this->input->post();
            $data  = array();
            if(!isset($datas['cart'])){
                throw new CI_MyException(1,'请返回选择菜品');
            }
            foreach ($datas['cart'] as $key => $value) {
                $arr = array();
                $arr['dishId'] = $key;
                $arr['num']    = $value['num'];
                $arr['boardId']= $this->input->post('boardId');
                $arr['remark'] = $value['remark'];
                $data[] = $arr;
            }
        }
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $phone= $this->input->post('phone');
        $address = $this->input->post('address');
        $delivery= '';
        $remark = $this->input->post('remark');

  		$pay = $this->input->post('pay') ? $this->input->post('pay') : 0;

  		//执行业务逻辑
  		return $this->dishOrderAo->placeOrder($userId,$clientId,$data,$pay,$remark,$type,$code,$name,$phone,$address);
  	}

    /**
     * @view json
     * 获取订单信息
     */
    public function getOrderInfo(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];

            $orderNo = $this->input->post('orderNo');

            return $this->dishOrderAo->getOrderInfo($userId,$orderNo);
        }
    }

    /**
     * @view json
     * 我的订单
     */
    public function myOrder(){
        // if($this->input->is_ajax_request()){
            //检查输入参数    
            $user = $this->argv->checkGet(array(
                array('userId','option'),
            ));
            $userId = $user['userId'];

            //检查权限
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            return $this->dishOrderAo->myOrder($userId,$clientId);
        // }
    }

    /**
     * @view json
     * 我的订单详情
     */
    public function myOrderDetail(){
        //检查输入参数    
        $user = $this->argv->checkGet(array(
            array('userId','option'),
        ));
        $userId = $user['userId'];

        //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);
        $clientId = $client['clientId'];

        // $orderNo = $this->input->get('orderNo') ? $this->input->get('orderNo') : '144228320210007113402';
        $orderNo = $this->input->get('orderNo');
        return $this->dishOrderAo->myOrderDetail($userId,$clientId,$orderNo);
    }

    /**
     * @view json
     * 商家受理订单
     */
    public function accept(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $orderNo = $this->input->post('orderNo');
            return $this->dishOrderAo->accept($userId,$orderNo);
        }
    }

    /**
     * @view json
     * 商家取消订单
     */
    public function cancel(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $orderNo = $this->input->get('orderNo');
            $clientId = $this->session->userdata('clientId');
            return $this->dishOrderAo->cancle($userId,$orderNo,$clientId);
        }
    }

    /**
     * @view json
     * 商家确定取消订单
     */
    public function realyCancle(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $orderNo = $this->input->get('orderNo');
            return $this->dishOrderAo->realyCancle($userId,$orderNo);
        }
    }

    /**
     * @view json
     * 计算总价
     */
    public function cal(){
        if($this->input->is_ajax_request()){
            $dataLimit['pageSize'] = $this->input->get('pageSize');
            $dataLimit['pageIndex']= $this->input->get('pageIndex');
            $boardId = $this->input->get('boardId');
            $states  = $this->input->get('states') ? $this->input->get('states') : '0';
            $dataWhere['boardId'] = $boardId;
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            return $this->dishOrderAo->cal($userId,$dataWhere,$dataLimit,$states);
        }
    }

    /**
     * @view json
     * 餐桌结账
     */
    public function closeAccounts(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $boardId = $this->input->get('boardId');
            return $this->dishOrderAo->closeAccounts($userId,$boardId);
        }
    }

    /**
     * @view json
     * 订单扫一扫
     */
    public function scan(){
        if($this->input->is_ajax_request()){
            //检查输入参数    
            $user = $this->argv->checkGet(array(
                array('userId','option'),
            ));
            $userId = $user['userId'];
            //检查权限
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            //扫面时效性写入缓存
            $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
            $scan = 1;
            $this->cache->save('s'.$clientId, $scan, 60);
            return 1;
        }
    }

    /**
     * @view json
     * 取消预约
     */
    public function forbid(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $orderNo = $this->input->post('orderNo');
            $data = $this->input->post();
            return $this->dishOrderAo->forbid($userId,$orderNo,$data);
        }
    }

    public function test(){
        // echo 1;
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        var_dump($this->cache->get('s11384'));
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */