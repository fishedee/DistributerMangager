<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Board extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userAo','userAo');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->library('argv','argv');
		$this->load->model('board/boardAo','boardAo');
        $this->load->model('client/clientWxLoginAo','clientWxLoginAo');
        $this->load->model('client/clientAo','clientAo');
    }

    /**
     * @view json
     * 查询餐桌 
     */
    public function search(){
    	if($this->input->is_ajax_request()){
    		//检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];

			$dataLimit = $this->argv->checkGet(array(
				array('pageIndex','require'),
				array('pageSize','require'),
			));
			$dataWhere = $this->argv->checkGet(array(
                array('boardTypeId','option')
            ));
			return $this->boardAo->search($userId,$dataWhere,$dataLimit);
    	}
    }

    /**
     * @view json
     * 增加餐桌
     */
    public function add(){
    	if($this->input->is_ajax_request()){
    		//检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
			$data   = $this->input->post('data');
			return $this->boardAo->add($userId,$data);
    	}
    }

    /**
     * @view json
     * 判断是否为微信浏览器
     */
    public function isWeixin(){ 
    	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	    $is_weixin = strpos($agent, 'micromessenger') ? true : false ;   
	    if($is_weixin){
	        return true;
	    }else{
	        return false;
	    }
  	}

    /**
     * @view json
     * 处理扫描
     */
    public function scan(){
        if($_GET){
            $boardNum = $this->input->get('boardNum');
            //检查输入参数
            $data = $this->argv->checkGet(array(
                array('userId','require')
            ));
            $userId   = $data['userId'];
            $callback = 'http://'.$userId.'.'.$_SERVER['HTTP_HOST'].'/board/scan?boardNum='.$boardNum;
            $clientId = $this->session->userdata('clientId');
            if(!$clientId){
                $this->clientWxLoginAo->login($data['userId'],$callback);
            }
            $this->clientAo->scan($userId,$clientId,$boardNum);
        }
    }

    /**
     * @view json
     * 判断扫描时效性
     */
    public function scanTime(){
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        $clientId = $this->session->userdata('clientId');
        if(!$this->cache->get($clientId)){
            throw new CI_MyException(1,'请重新扫描二维码');
        }else{
            return 1;
        }
    }

    /**
     * @view json
     * 再次确认
     */
    public function scanConfirm(){
        if($this->input->is_ajax_request()){
            //检查输入参数
            $data = $this->argv->checkGet(array(
                array('userId','require')
            ));
            $userId   = $data['userId'];
            $url = $this->input->get('url');
            return $this->boardAo->scanConfirm($userId,$url);
        }
    }

    /**
     * @view json
     * 获取餐桌内容
     */
    public function getBoard(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $boardId= $this->input->post('boardId');
            return $this->boardAo->getBoardInfo($userId,$boardId);
        }
    }

    /**
     * @view json
     * 修改
     */
    public function mod(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $data   = $this->input->post('data');
            $boardId= $this->input->post('boardId');
            return $this->boardAo->modBack($userId,$boardId,$data);
        }
    }

    /**
     * @view json
     * 取消
     */
    public function cancel(){
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        //检查输入参数    
        $user = $this->argv->checkGet(array(
            array('userId','option'),
        ));
        $userId = $user['userId'];
        //检查权限
        $this->load->model('client/clientLoginAo', 'clientLoginAo');
        $client = $this->clientLoginAo->checkMustLogin($userId);
        $clientId = $client['clientId'];
        $this->cache->delete($clientId);
    }

    /**
     * @view json
     * 结账
     */
     public function close(){
         if($this->input->is_ajax_request()){
             //检查权限
             $user = $this->loginAo->checkOrder(
                 $this->userPermissionEnum->ORDER_DINNER
             );
             $userId = $user['userId'];
             $boardId= $this->input->get('boardId');
             return $this->boardAo->close($userId,$boardId);
         }
     }


    public function test(){
      $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }
}

/* End of file welcome.php */ 
/* Location: ./application/controllers/welcome.php */
