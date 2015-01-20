<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/userAo','userAo');
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->library('argv','argv');
    }
	
	public function getAllType()
	{
		$data = array(
			'code'=>0,
			'msg'=>'',
			'data'=>$this->userTypeEnum->names
		);
		$this->load->view(
			'json',
			$data
		);
	}
	
	public function getAllPermission()
	{
		$data = array(
			'code'=>0,
			'msg'=>'',
			'data'=>$this->userPermissionEnum->names
		);
		$this->load->view(
			'json',
			$data
		);
	}
	
	public function search()
	{
		//检查权限
		$result = $this->loginAo->isAdmin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		
		//检查输入参数		
		$result = $this->argv->getOptionInput(array('name','type','company','phone'));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return;
		}
		$dataWhere = $result["data"];
		
		$result = $this->argv->getRequireInput(array('pageIndex','pageSize'));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return;
		}
		$dataLimit = $result["data"];
			
		//执行业务逻辑
		$data = $this->userAo->search($dataWhere,$dataLimit);
		if( $data["code"] != 0 ){
			$this->load->view('json',$data);
			return;
		}
		$this->load->view('json',$data);
	}
	
	public function get()
	{
		//检查权限
		$result = $this->loginAo->isAdmin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		
		//检查输入参数
		$result = $this->argv->getRequireInput(array('userId'));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$userId = $result["data"]["userId"];
		
		//执行业务逻辑
		$data = $this->userAo->get(
			$userId
		);
		$this->load->view('json',$data);
	}
	
	public function add()
	{
		//检查权限
		$result = $this->loginAo->isAdmin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		
		//检查输入参数
		$result = $this->argv->postRequireInput(array('name','type','phone','company'));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$data = $result["data"];
		
		$result = $this->argv->postDefaultInput(array('permission'),array());
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$data = array_merge($data,$result["data"]);
		
		//执行业务逻辑
		$data = $this->userAo->add(
			$data
		);
		$this->load->view('json',$data);
	}
	
	public function del()
	{
		//检查权限
		$result = $this->loginAo->isAdmin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		
		//检查输入参数
		$result = $this->argv->postRequireInput(array('userId'));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$userId = $result["data"]['userId'];
		
		//执行业务逻辑
		$data = $this->userAo->del(
			$userId
		);
		$this->load->view('json',$data);
	}
	
	public function mod()
	{
		//检查权限
		$result = $this->loginAo->isAdmin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		
		//检查输入参数
		$result = $this->argv->postRequireInput(array('userId','name','type','phone','company'));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$data = $result["data"];
		
		$result = $this->argv->postDefaultInput(array('permission'),array());
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$data = array_merge($data,$result["data"]);
		
		//执行业务逻辑
		$data = $this->userAo->mod(
			$data['userId'],
			$data
		);
		$this->load->view('json',$data);
	}
	
	public function modPassword()
	{
		//检查权限
		$result = $this->loginAo->isAdmin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		
		//检查输入参数
		$result = $this->argv->postRequireInput(array('userId','password'));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$userId = $result["data"]["userId"];
		$password = $result["data"]["password"];
		
		//执行业务逻辑
		$data = $this->userAo->modPassword(
			$userId,
			$password
		);
		$this->load->view('json',$data);
	}
	
	public function modMyPassword()
	{
		//检查权限
		$result = $this->loginAo->islogin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$userId = $result['data']['userId'];
		
		//检查输入参数
		$result = $this->argv->postRequireInput(array('oldPassword','newPassword'));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$oldPassword = $result["data"]["oldPassword"];
		$newPassword = $result["data"]["newPassword"];
		
		//执行业务逻辑
		$data = $this->userAo->modPasswordByOld(
			$userId,
			$oldPassword,
			$newPassword
		);
		$this->load->view('json',$data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
