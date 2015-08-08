<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LuckyDraw extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->model('luckydraw/luckyDrawAo','luckyDrawAo');
		$this->load->model('luckydraw/luckyDrawStateEnum','luckyDrawStateEnum');
		$this->load->model('luckydraw/luckyDrawTypeEnum','luckyDrawTypeEnum');
		$this->load->model('luckydraw/luckyDrawMethodEnum','luckyDrawMethodEnum');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function getAllType()
	{
		return $this->luckyDrawTypeEnum->names;
	}

	/**
	* @view json
	*/
	public function getAllState()
	{
		return $this->luckyDrawStateEnum->names;
	}
	
	/**
	* @view json
	*/
	public function search()
	{
		//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('title','option'),
			array('summary','option'),
			array('state','option'),
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		return $this->luckyDrawAo->search($userId,$dataWhere,$dataLimit);
	}
	
	/**
	* @view json
	*/
	public function get()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('luckyDrawId','require'),
		));
		$luckyDrawId = $data['luckyDrawId'];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		return $this->luckyDrawAo->get($userId,$luckyDrawId);
	}
	
	/**
	* @view json
	*/
	public function add()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('title','require'),
			array('method','require'),
			array('summary','require'),
			array('state','require'),
			array('beginTime','require'),
			array('endTime','require'),
			array('commodity','option',array()),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		$this->luckyDrawAo->add($userId,$data);
	}
	
	/**
	* @view json
	*/
	public function del()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('luckyDrawId','require'),
		));
		$luckyDrawId = $data['luckyDrawId'];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		$this->luckyDrawAo->del($userId,$luckyDrawId);
	}
	
	/**
	* @view json
	*/
	public function mod()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('luckyDrawId','require'),
		));
		$luckyDrawId = $data['luckyDrawId'];
		
		$data = $this->argv->checkPost(array(
			array('title','require'),
			array('method','require'),
			array('summary','require'),
			array('state','require'),
			array('beginTime','require'),
			array('endTime','require'),
			array('commodity','option',array()),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		$this->luckyDrawAo->mod($userId,$luckyDrawId,$data);
	}

	/**
	* @view json
	*/
	public function getResult()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('luckyDrawId','require'),
		));
		$luckyDrawId = $data['luckyDrawId'];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		return $this->luckyDrawAo->getResult($userId,$luckyDrawId);
	}
	
	/**
	* @view json
	*/
	public function draw()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId','require'),
			array('luckyDrawId','require'),
			array('name','require'),
			array('phone','require'),
		));
		
		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($data['userId']);
		
		//执行业务逻辑
		return $this->luckyDrawAo->luckyDraw(
			$data['userId'],
			$client['clientId'],
			$data['luckyDrawId'],
			$data['name'],
			$data['phone']
		);
	}

	/**
	* @view json
	*/
	public function getClientResult()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
			array('luckyDrawId','require'),
		));
		
		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($data['userId']);
		
		//执行业务逻辑
		return $this->luckyDrawAo->getClientResult(
			$data['userId'],
			$client['clientId'],
			$data['luckyDrawId']
		);
	}

	/**
	* @view json
	*/
	public function getClientAllResult()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));
		
		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($data['userId']);
		
		//执行业务逻辑
		return $this->luckyDrawAo->getClientAllResult(
			$data['userId'],
			$client['clientId']
		);
	}

	/**
	 * @view json
	 * 判断合理性
	 */
	public function judge(){
		if($this->input->is_ajax_request()){
			$card_id = $this->input->post('card_id');
			$list_id = $this->input->post('list_id');
			return $this->luckyDrawAo->judge($list_id);
		}
	}

	/**
	 * @view json
	 */
	public function getLuckyMethod(){
		return $this->luckyDrawMethodEnum->names;
	}

	/**
	 * @view json
	 * 给抽奖前端显示其他中奖用户
	 */
	 public function winningList(){
	 	//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId','require'),
			array('luckyDrawId','require'),
		));

		//检查权限
		//$client = $this->clientLoginAo->checkMustLogin($data['userId']);

		$sql="select nickName,title from t_lucky_draw_client left join t_client on t_lucky_draw_client.clientId=t_client.clientId where luckyDrawId='".$data['luckyDrawId']."' and nickName not like '' and nickName not like '用户没关注' order by luckyDrawClientId desc limit 10;";
		$data = $this->db->query($sql)->result_array();
		return $data;
	 }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
