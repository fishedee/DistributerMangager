<?php 
	/**
	 * author: zzh
	 */
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Chips extends CI_Controller{

		private $userId;
		private $clientId;

		public function __construct(){
			parent::__construct();
			$this->load->model('user/user_model','usermodel'); // 加载user模型
			// $this->load->model('chipsModel/chipsDb','chipsmodel'); // 加载chips模型
			$this->load->model('chips/chipsRecordDb','record'); //订购记录模型
			$this->load->model('chips/chipsOrderAo','chipsOrderAo');
			$this->load->model('chips/chipsPowerDb','power');

			$this->load->model('chips/chipsAo','chipsAo');
			$this->load->library('argv', 'argv');
			$clientId = $this->session->userdata('clientId');
			$this->clientId = $clientId;
			//众筹商品的状态
			$chipsInfo = $this->chipsAo->getChips(0,0,array());
			$chipsInfo = $chipsInfo['data'];
			foreach ($chipsInfo as $key => $value) {
				$update = $this->chipsAo->changeStart($value['start_time'],$value['end_time'],$value['chips_id'],$value['start']);
			}
		}

		/**
		 * @view json
		 * 增加众筹商品
		 */
		public function add(){
			if($this->input->is_ajax_request()){
				$data = $this->input->post('data');
				return $this->chipsAo->addChips($data);
			}
		}

		/**
		 * @view json
		 */
		public function getChipsBack(){
			if($this->input->is_ajax_request()){
				$userId = $userId = $this->session->userdata('userId');
				if($this->session->userdata('userId')){
					$result = $this->usermodel->checkPermission($this->session->userdata('userId'));
					if(!$result){
						throw new CI_MyException(1,'您没有权限');
					}
				}
				$mobile = '';
				if(isset($_GET['mobile'])){
					$mobile = 1;
				}
				$dataLimit = $this->argv->checkGet(array(
					array('pageIndex','require'),
					array('pageSize','require'),
				));

				//查询自己的众筹商品
				$chipsInfo = $this->chipsAo->getChips($userId,$mobile,$dataLimit);
				return $chipsInfo;
			}
		}

		//查看众筹商品 -- 手机端获取
		public function getChips(){
			if($this->input->is_ajax_request()){
				$argv = $this->argv->check(array(
		            array('userId', 'require')
		        ));
		        $userId = $argv['userId'];

				$mobile = '';
				if(isset($_GET['mobile'])){
					$mobile = 1;
				}

				//查询自己的众筹商品
				$chipsInfo = $this->chipsAo->getChips($userId,$mobile,array());
				foreach ($chipsInfo['data'] as $key => $value) {
					$end = strtotime($value['end_time']);
					$dis = ceil(($end - time())/86400);
					if($dis < 0 ){
						$dis = 0;
					}
					$chipsInfo['data'][$key]['distime'] = $dis;
				}
				// var_dump($chipsInfo);die;
				$data['code'] = 0;
				$data['msg']  = '';
				$data['data'] = $chipsInfo;
				echo json_encode($data);
			}
		}

		/**
		 * @view json
		 * 删除众筹商品
		 */
		public function del(){
			if($this->input->is_ajax_request()){

				$data = $this->input->post();
				$chips_id = $data['chips_id'];

				//执行删除
				$affected = $this->chipsAo->del($chips_id);

				return $affected;
			}
		}

		/**
		 * @view json
		 * 获取众筹商品的详细信息
		 */
		public function chipsDetail(){
			if($this->input->is_ajax_request()){


				if($this->input->post('back') == 1){
					$userId = $userId = $this->session->userdata('userId');
				}else{
					$argv = $this->argv->check(array(
			            array('userId', 'require')
			        ));
			        $userId = $argv['userId'];
				}

				$chips_id = $this->input->post('chips_id');

				return $this->chipsAo->chipsDetail($chips_id,$userId);
			}
		}

		/**
		 * @view json
		 * 更新众筹商品
		 */
		public function updateChips(){
			if($this->input->is_ajax_request()){

				$data = $this->input->post('data');
				$chips_id = $this->input->post('chips_id');
				
				$affected = $this->chipsAo->updateChips($chips_id,$data);
				return $affected;
			}
		}

		//上下架
		public function upOrDown(){
			if($this->input->is_ajax_request()){
				$chips_id = $this->input->post('chips_id');
				$affected = $this->chipsAo->upOrDown($chips_id);
				if($affected > 0){
					echo 1;
				}else{
					echo '更新失败';
				}
			}
		}
		//统计图所需要的数据
		public function line(){
			if($this->input->is_ajax_request()){
				$argv = $this->argv->check(array(
		            array('userId', 'require')
		        ));
		        $userId = $argv['userId'];
				$chips_id = $this->input->post('chips_id');
				$chipsInfo = $this->chipsAo->chipsDetail($chips_id,$userId);
				$y[] = $chipsInfo['oldprice'];
				$x[] = 0;
				//众筹记录
				$recordInfo = $this->record->recordNum($chips_id);
				// var_dump($recordInfo);die;
				if($recordInfo){
					foreach ($recordInfo as $key => $value) {
						$y[] = $value['newprice'];
						$x[] = $value['num'];
					}
				}
				foreach ($y as $key => $value) {
					$y[$key] = (double)$value;
				}
				$data['x'] = $x;
				$data['y'] = $y;
				echo json_encode($data);
			}
		}

		//检测密码
		public function checkPassword(){
			if($this->input->is_ajax_request()){
				$chips_id = $this->input->post('chips_id');
				$password = $this->input->post('password');
				$result   = $this->chipsAo->checkPassword($chips_id,md5($password));
				if($result){
					echo json_encode(md5($password));
				}else{
					echo 0;
				}
			}
		}

		//检测GET方式进入的
		public function checkGetPassword(){
			if($this->input->is_ajax_request()){
				$chips_id = $this->input->post('chips_id');
				$password = $this->input->post('password');
				$result   = $this->chipsAo->checkPassword($chips_id,$password);
				if($result){
					echo 1;
				}else{
					echo 0;
				}
				// var_dump($result);die;
			}
		}

		//支付成功后 价格更新
		public function firstAfter(){
			if($this->input->is_ajax_request()){
				$chips_id = $this->input->post('chips_id');
				$num = $this->input->post('num');
				$chips_order_id = $this->input->post('chips_order_id');
				//更新价格
				$this->chipsAo->payFirstAfter($chips_id,$num);
				//增加众筹记录
				$this->record->addChipsRecord($this->clientId,$chips_id,$chips_order_id);
				//更新订单状态
				$this->chipsOrderAo->firstPayOver($chips_order_id);
				echo 1;
			}
		}

		//活动状态
		public function chipsStart(){
			if($this->input->is_ajax_request()){
				echo json_encode($this->chipsAo->getStart($this->input->post('chips_id')));
			}
		}
	}
 ?>