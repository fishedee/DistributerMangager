<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Poll extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('poll/pollAo','pollAo');
		$this->load->library('argv','argv');
    }

    /**
     * @view json
     * 获取报名人数
     */
    public function getPoll(){
    	//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId','require'),
		));
		$userId = $data['userId'];
		return $this->pollAo->getPoll($userId);
    }

    /**
     * @view json
     * 投票
     */
    public function vote(){
    	if($this->input->is_ajax_request()){
    		//检查输入参数
			$data = $this->argv->checkPost(array(
				array('userId','require'),
			));
			$userId = $data['userId'];
			$clientId = $this->input->post('clientId');
			$pollId = $this->input->post('pollId');
			return $this->pollAo->vote($userId,$clientId,$pollId);
    	}
    }

    /**
     * @view json
     * 二维码海报
     */
    public function poster(){
        $openId = 'oLZlmt0Z5KJWsysjw2xxkx-9h0w8';
        $userId = '10062';
        $pollId = 10002;
        $this->pollAo->poll($userId,$openId);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
