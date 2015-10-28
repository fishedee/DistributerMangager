<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BoardDate extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userAo','userAo');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->library('argv','argv');
        $this->load->model('board/boardDateAo','boardDateAo');
        $this->load->model('client/clientLoginAo','clientLoginAo');
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
			$dataWhere = array();
			return $this->boardDateAo->search($userId,$dataWhere,$dataLimit);
    	}
    }

    /**
     * @view json
     * 增加预定时间
     */
    public function add(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $data   = $this->input->post('data');
            return $this->boardDateAo->add($userId,$data);
        }
    }

    /**
     * @view json
     * 获取订餐时间信息
     */
    public function getDate(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $dateId = $this->input->get('dateId');
            return $this->boardDateAo->getDate($userId,$dateId);
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
            $dateId = $this->input->post('dateId');
            return $this->boardDateAo->mod($userId,$dateId,$data);
        }
    }

    /**
     * @view json
     * 删除
     */
    public function del(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $dateId = $this->input->get('dateId');
            return $this->boardDateAo->del($userId,$dateId);
        }
    }

    /**
     * @view json
     * 前台获取订桌时间
     */
    public function getOrderTime(){
        // 检查输入参数
        $data = $this->argv->checkGet(array(
            array('userId','require')
        ));
        $userId   = $data['userId'];
        // $userId = 10007;
        for ($i=0; $i < 3; $i++) { 
            $now = date('Y-m-d',strtotime("+".$i."day"));
            $now_w = date('w');
            if($now_w == 0){
                $now_w = 7;
            }
            $now_w = $now_w + $i;
            if($now_w > 7){
                $now_w = abs($now_w - 7);
            }
            $arr[$i]['date'] = $now;
            $arr[$i]['week'] = $now_w;
        }
        $result = $this->boardDateAo->getOrderTime($userId,$arr);
        return $result;
    }

    /**
     * @view json
     * 检测用户是否需要验证
     */
    public function checkVerify(){
        if($this->input->is_ajax_request()){
            //检查输入参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require'),
            ));
            $userId = $data['userId'];
            // 检查权限
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            return $this->boardDateAo->checkVerify($userId,$clientId);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
