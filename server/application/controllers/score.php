<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Score extends CI_Controller {

	public function __construct(){
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->model('client/scoreAo','scoreAo');
		$this->load->library('argv','argv');
    }

    /**
     * @view json
     * 获取积分日志
     */
    public function getLog(){
    	if($this->input->is_ajax_request()){
		 	$argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            return $this->scoreAo->getLog($clientId);
    	}
    }

    /**
     * @view json
     * 分享朋友圈的必要参数
     */
    public function getEnjoyParameters(){
        if($this->input->is_ajax_request()){
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $url = $this->input->get('url');
            return $this->scoreAo->getEnjoyParameters($userId,$url);
        }
    }

    /**
     * @view json
     * 分享朋友圈成功
     */
    public function enjoyShareSuccess(){
        if($this->input->is_ajax_request()){
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            $url = $this->input->get('url');
            return $this->scoreAo->enjoyShareSuccess($userId,$clientId,$url);
        }
    }

    /**
     * @view json
     * 分享给朋友成功
     */
    public function enjoyFriendSuccess(){
        if($this->input->is_ajax_request()){
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            $url = $this->input->get('url');
            return $this->scoreAo->enjoyFriendSuccess($userId,$clientId,$url);
        }
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
