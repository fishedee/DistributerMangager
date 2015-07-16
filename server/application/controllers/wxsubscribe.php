<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wxsubscribe extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('weixin/wxSubscribeEnum','wxSubscribeEnum');
		$this->load->model('weixin/wxSubscribeStateEnum','wxSubscribeStateEnum');
		$this->load->model('weixin/wxSubscribeAo','wxSubscribeAo');
		$this->load->model('user/loginAo','loginAo');
		$this->load->library('argv','argv');
		
	}

		
		/**
		 * @view json
		 * 获取关注回复类型
		 */
		public function getAllType()
		{
			return $this->wxSubscribeEnum->names;
		}
		
		/**
		 * @view json
		 * 获取发布状态类型
		 */
		public function getSubscribeStateType()
		{
			return $this->wxSubscribeStateEnum->names;
		}
	
		/**
		 * @view json
		 * 获取关注回复列表
		 */
		public function search()
		{
			//检查输入参数
			$dataWhere = $this->argv->checkGet(array(
					array('title','option'),
					array('remark','option')
			));
		
			$dataLimit = $this->argv->checkGet(array(
					array('pageIndex','option'),
					array('pageSize','option'),
			));
			//检查权限
			$userId = $this->loginAo->checkMustLogin();
			$userId =$userId['userId'];
						
			//检查是否填写微信号
			$this->load->model('user/userAppAo','userAppAo');
			$userApp = $this->userAppAo->get($userId);
			$this->userAppAo->checkWeixinNum($userApp);
			//执行业务逻辑
			return $this->wxSubscribeAo->search($userId,$dataWhere,$dataLimit);		
		}	
		
		/**
		 * @view json
		 * 多图文列表内容获取
		 */
		public function graphicGet()
		{
			//检查输入参数
			$data = $this->argv->checkGet(array(
					array('weixinSubscribeId','require'),
			));
			$weixinSubscribeId = $data['weixinSubscribeId'];
		
			//检查权限
			$userId = $this->loginAo->checkMustLogin();
			$userId =$userId['userId'];
		
			//执行业务逻辑
			return $this->wxSubscribeAo->graphicGet($userId,$weixinSubscribeId);
		}
		
		/**
		 * @view json
		 * 添加多图文信息
		 */
		public function graphicAdd()
		{
			//检查输入参数
			if(strstr($_SERVER['HTTP_REFERER'], 'graphic')){
				$data = $this->argv->checkPost(array(
					array('title','require'),				
					array('remark','require'),
					array('graphic','option',array()),
				));
			
			}elseif(strstr($_SERVER['HTTP_REFERER'], 'singleGraphic')){
				$data = $this->argv->checkPost(array(
					array('title','require'),				
					array('Title','require'),
					array('Description','require'),
					array('Url','require'),
					array('PicUrl','require'),
					array('remark','require'),
				));
			
			}elseif(strstr($_SERVER['HTTP_REFERER'], 'theText')){
				$data = $this->argv->checkPost(array(
					array('title','require'),	
					array('remark','require'),
					array('Description','require'),
				));
			
			}
			
			//检查权限
			$userId = $this->loginAo->checkMustLogin();
			$userId = $userId['userId'];
		
			//执行业务逻辑
			return $this->wxSubscribeAo->graphicAdd($userId,$data);
			
		}
		
		/**
		 * @view json
		 * 修改多图文内容
		 */
		public function graphicMod(){
			//检查输入参数
			if(strstr($_SERVER['HTTP_REFERER'], 'graphic')){
				$data = $this->argv->checkPost(array(
					array('weixinSubscribeId','weixinSubscribeId'),
					array('title','require'),				
					array('remark','require'),
					array('graphic','option',array()),
				));
			
			}elseif(strstr($_SERVER['HTTP_REFERER'], 'singleGraphic')){
				$data = $this->argv->checkPost(array(
					array('weixinSubscribeId','weixinSubscribeId'),
					array('title','require'),				
					array('Title','require'),
					array('Description','require'),
					array('Url','require'),
					array('PicUrl','require'),
					array('remark','require'),
					array('graphic','option',array()),
				));
			
			}elseif(strstr($_SERVER['HTTP_REFERER'], 'theText')){
				$data = $this->argv->checkPost(array(
					array('title','require'),	
					array('remark','require'),
					array('Description','require'),
				));
			
			}
			
			//检查权限
			$userId = $this->loginAo->checkMustLogin();
			$userId = $userId['userId'];
			
			$this->wxSubscribeAo->del($userId,$data['weixinSubscribeId'],$data);//第三参数，更新文件状态
			$this->wxSubscribeAo->graphicAdd($userId,$data);
		}
		
		/**
		 * @view json
		 * 发布被关注回复内容
		 */
		public function SubscribePublish(){
			//检查输入参数
			$data = $this->argv->checkPost(array(
					array('weixinSubscribeId','require'),
			));
			
			//检查权限
			$userId = $this->loginAo->checkMustLogin();
			$userId = $userId['userId'];
			
			//执行业务逻辑
			return $this->wxSubscribeAo->publish($userId,$data['weixinSubscribeId']);
		}
		
		/**
		 * @view json
		 * 删除被关注回复内容
		 */
		public function del(){
			//检查输入参数
			$data = $this->argv->checkPost(array(
					array('weixinSubscribeId','require'),
			));
				
			//检查权限
			$userId = $this->loginAo->checkMustLogin();
			$userId = $userId['userId'];
			
			//执行业务逻辑
			return $this->wxSubscribeAo->del($userId,$data['weixinSubscribeId']);
		}
		
		/**
		 * @view json
		 * 获取已发布信息
		 */
		public function getMysubscribe(){
			//检查权限
			$userId = $this->loginAo->checkMustLogin();
			$userId = $userId['userId'];
			
			//执行业务逻辑
			return $this->wxSubscribeAo->getMysubscribe($userId);
		}

		//获取素材的ID信息
		public function getKeyResponseId(){
			//检查权限
			$userId = $this->loginAo->checkMustLogin();
			$userId = $userId['userId'];
			$result = $this->wxSubscribeAo->getKeyResponseId($userId);
			foreach ($result as $key => $value) {
				$arr[$value['weixinSubscribeId']] = $value['weixinSubscribeId'];
			}
			echo json_encode($arr);
		}

		//删除关键词的自动回复
		public function keyResponseDel(){
			if($this->input->is_ajax_request()){
				//检查权限
				$userId = $this->loginAo->checkMustLogin();
				$userId = $userId['userId'];
				$keyResponseId = $this->input->post('keyResponseId');
				$result = $this->wxSubscribeAo->keyResponseDel($keyResponseId);
				if($result){
					echo 1;
				}else{
					echo 0;
				}
			}
		}

		/**
		 * @view json
		 * 增加自动回复信息
		 */
		public function addKey(){
			if($this->input->is_ajax_request()){
				//检查权限
				$userId = $this->loginAo->checkMustLogin();
				$userId =$userId['userId'];
				$datas = $this->input->post('data');
				// var_dump($data);die;
				$data['keyWord']= $datas['keyWord'];
				$data['weixinSubscribeId'] = $datas['weixinSubscribeId'][0]['weixinSubscribeId'];
				$data['userId'] = $userId;
				return $this->wxSubscribeAo->addKey($data);
			}
		}

		/**
		 * @view json
		 * 更新
		 */
		public function updateKey(){
			if($this->input->is_ajax_request()){
				//检查权限
				$userId = $this->loginAo->checkMustLogin();
				$userId =$userId['userId'];
				$datas = $this->input->post('data');
				$keyResponseId = $this->input->post('keyResponseId');
				$data['keyWord']= $datas['keyWord'];
				$data['weixinSubscribeId'] = $datas['weixinSubscribeId'][0]['weixinSubscribeId'];
				$data['userId'] = $userId;
				return $this->wxSubscribeAo->updateKey($keyResponseId,$data);
			}
		}

		/**
		 * @view json
		 * 查看关键词自动回复
		 */
		public function keySearch(){
			$dataWhere = array();
			$dataLimit = $this->argv->checkGet(array(
					array('pageIndex','option'),
					array('pageSize','option'),
			));
		
			//检查权限
			$userId = $this->loginAo->checkMustLogin();
			$userId =$userId['userId'];
				
			$dataWhere['userId'] = $userId;

			//检查是否填写微信号
			$this->load->model('user/userAppAo','userAppAo');
			$userApp = $this->userAppAo->get($userId);
			$this->userAppAo->checkWeixinNum($userApp);

			//执行业务逻辑
			return $this->wxSubscribeAo->keySearch($dataWhere,$dataLimit);
			
		}

		/**
		 * @view json
		 * 获取关键词自动回复信息
		 */
		public function getKeyResponseInfo(){
			if($this->input->is_ajax_request()){
				//检查权限
				$userId = $this->loginAo->checkMustLogin();
				$userId =$userId['userId'];

				$keyResponseId = $this->input->post('keyResponseId');
				return $this->wxSubscribeAo->getKeyResponseInfo($keyResponseId);
			}
		}
		
}