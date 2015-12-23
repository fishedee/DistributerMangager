<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserShow extends CI_Controller {

	public function __construct(){
        parent::__construct();
        $this->load->model('user/userShowAo','userShowAo');
        $this->load->model('client/clientLoginAo', 'clientLoginAo');
        $this->load->library('argv','argv');
    }

    /**
     * @view json
     * 获取买家秀上传的图片
     */
    public function getShowPic(){
    	if($this->input->is_ajax_request()){
    		//检查输入参数
        	$data = $this->argv->checkGet(array(
            	array('userId', 'require')
        	));
        	$userId = $data['userId'];
    		//检查权限
        	$client = $this->clientLoginAo->checkMustLogin($userId);
        	$clientId = $client['clientId'];
        	return $this->userShowAo->getShowPic($userId,$clientId);
    	}
    }

    /**
     * @view json
     * 删减图片
     */
    public function del(){
        if($this->input->is_ajax_request()){
            //检查输入参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require')
            ));
            $userId = $data['userId'];
            //检查权限
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            $showId = $this->input->post('showId');
            return $this->userShowAo->del($userId,$clientId,$showId);
        }
    }

    //测试
    public function test(){
    	return $this->userShowAo->test();
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
