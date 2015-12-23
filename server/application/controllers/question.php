<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Question extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->model('question/questionAo','questionAo');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function search(){
		//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('question','option'),
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustLogin();
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->questionAo->search($userId,$dataWhere,$dataLimit);
	}

	/**
	 * @view json
	 */
	public function seachCollect(){
		//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			// array('question','option'),
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustLogin();
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->questionAo->seachCollect($userId,$dataWhere,$dataLimit);
	}

	/**
	 * @view json
	 * 提交申请
	 */
	public function add(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $user['userId'];
			$data = $this->input->post('data');
        	return $this->questionAo->add($userId,$data);
		}
	}

	/**
	 * @view json
	 */
	public function get(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $user['userId'];
			$questionId = $this->input->get('questionId');
        	return $this->questionAo->get($userId,$questionId);
		}
	}

	/**
	 * @view json
	 * 更新
	 */
	public function mod(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $user['userId'];
			$data = $this->input->post('data');
			$questionId = $this->input->post('questionId');
        	return $this->questionAo->mod($userId,$questionId,$data);
		}
	}

	/**
	 * @view json
	 * 删除
	 */
	public function del(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $user['userId'];
			$questionId = $this->input->get('questionId');
			return $this->questionAo->del($userId,$questionId);
		}
	}

	/**
	 * @view json
	 * 前端获取问题
	 */
	public function getQuestion(){
		if($this->input->is_ajax_request()){
			//检查输入参数    
			$data = $this->argv->checkGet(array(
				array('userId','require'),
			));
			$userId = $data['userId'];
			return $this->questionAo->getQuestion($userId);
		}
	}

	/**
	 * @view json
	 * 收集评价
	 */
	public function collect(){
		if($this->input->is_ajax_request()){
			//检查输入参数    
			$data = $this->argv->checkGet(array(
				array('userId','require'),
			));
			$userId = $data['userId'];

			//检查权限
        	$client = $this->clientLoginAo->checkMustLogin($userId);
        	$clientId = $client['clientId'];

			$data = $this->input->post('data');
			$data = json_decode($data,true);
			return $this->questionAo->collect($userId,$clientId,$data);
		}
	}

	/**
	 * @view json
	 * 获取评论
	 */
	public function getCollect(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $user['userId'];
			$collectId = $this->input->get('collectId');
			return $this->questionAo->getCollect($userId,$collectId);
		}
	}

	/**
	 * @view json
	 * 修改评价
	 */
	public function modCollect(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $user['userId'];
			$data = $this->input->post('data');
			$collectId = $data['collectId'];
			unset($data['collectId']);
			return $this->questionAo->modCollect($collectId,$data);
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
